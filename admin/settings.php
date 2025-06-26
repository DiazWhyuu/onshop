<?php
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_title = trim($_POST['site_title']);
    $site_description = trim($_POST['site_description']);
    $admin_email = trim($_POST['admin_email']);
    $phone_number = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    
    // Validasi
    $errors = [];
    if (empty($site_title)) $errors['site_title'] = "Judul situs harus diisi";
    if (empty($admin_email) || !filter_var($admin_email, FILTER_VALIDATE_EMAIL)) $errors['admin_email'] = "Email admin tidak valid";
    
    // Handle file upload for logo
    $site_logo = null;
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['site_logo']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors['site_logo'] = "Hanya file JPG, PNG, atau GIF yang diizinkan";
        } else {
            $upload_dir = '../assets/images/';
            $file_ext = pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
            $site_logo = 'logo_' . uniqid() . '.' . $file_ext;
            
            if (!move_uploaded_file($_FILES['site_logo']['tmp_name'], $upload_dir . $site_logo)) {
                $errors['site_logo'] = "Gagal mengunggah logo";
            }
        }
    }
    
    if (empty($errors)) {
        // Save settings to database
        $settings = [
            'site_title' => $site_title,
            'site_description' => $site_description,
            'admin_email' => $admin_email,
            'phone_number' => $phone_number,
            'address' => $address
        ];
        
        if ($site_logo) {
            $settings['site_logo'] = $site_logo;
        }
        
        $conn->begin_transaction();
        try {
            foreach ($settings as $key => $value) {
                $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()");
                $stmt->bind_param("sss", $key, $value, $value);
                if (!$stmt->execute()) {
                    throw new Exception("Gagal menyimpan pengaturan: $key");
                }
            }
            $conn->commit();
            $_SESSION['success_message'] = "Pengaturan berhasil diperbarui";
            header("Location: settings.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $errors['general'] = "Gagal memperbarui pengaturan: " . $e->getMessage();
        }
    }
}

// Get current settings from database
$current_settings = [];
$sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_title', 'site_description', 'admin_email', 'phone_number', 'address', 'site_logo')";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

// Set default values if not found in database
$defaults = [
    'site_title' => 'UPVC Store',
    'site_description' => 'Toko Online Pintu dan Jendela UPVC',
    'admin_email' => 'admin@upvcstore.com',
    'phone_number' => '(021) 1234-5678',
    'address' => 'Jl. UPVC Modern No. 123, Jakarta Selatan, 12345, Indonesia',
    'site_logo' => 'logo.png'
];
$current_settings = array_merge($defaults, $current_settings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Situs - UPVC Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-[#e9e3dc] text-gray-800 min-h-screen flex">
    <!-- Sidebar -->
    <div class="bg-[#e9e3dc] text-gray-800 w-64 flex-shrink-0 border-r border-gray-800">
        <div class="p-6">
            <h1 class="text-2xl font-bold tracking-tight animate-slideInDown">UPVC Store Admin</h1>
        </div>
        <nav class="mt-6">
            <a href="dashboard.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Dashboard</a>
            <a href="products.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Produk</a>
            <a href="orders.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Pesanan</a>
            <a href="testimonials.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Testimonial</a>
            <div class="px-4 py-3 bg-stone-500 text-white">
                <span class="flex items-center justify-between">
                    <span class="font-semibold">Pengaturan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </div>
            <a href="../index.php" class="block px-4 py-3 text-white bg-stone-500 hover:bg-stone-400 mt-4 mx-4 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-external-link-alt mr-2"></i> Kunjungi Website
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Topbar -->
        <div class="bg-stone-500 border-b border-gray-700 p-4 flex justify-between items-center animate-slideInDown">
            <h2 class="text-xl font-semibold text-white">Pengaturan Situs</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-200">Halo, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="../logout.php" class="text-white hover:text-indigo-300 font-medium transition-all duration-300">Logout</a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 animate-slideInDown">Pengaturan Situs</h1>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6 animate-pulse">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errors['general'])): ?>
                <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6 animate-pulse">
                    <?php echo $errors['general']; ?>
                </div>
            <?php endif; ?>
            
            <div class="bg-stone-500 rounded-xl shadow-xl p-6 border border-gray-700 animate-fadeInUp">
                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="site_title" class="block text-white font-medium mb-2">Judul Situs</label>
                            <input type="text" id="site_title" name="site_title" value="<?php echo htmlspecialchars($current_settings['site_title']); ?>" class="w-full px-4 py-2 bg-[#e9e3dc] border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['site_title']) ? 'border-red-500' : ''; ?>" required>
                            <?php if (isset($errors['site_title'])): ?>
                                <p class="text-red-600 text-sm mt-1"><?php echo $errors['site_title']; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="admin_email" class="block text-white font-medium mb-2">Email Admin</label>
                            <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($current_settings['admin_email']); ?>" class="w-full px-4 py-2 bg-[#e9e3dc] border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['admin_email']) ? 'border-red-500' : ''; ?>" required>
                            <?php if (isset($errors['admin_email'])): ?>
                                <p class="text-red-600 text-sm mt-1"><?php echo $errors['admin_email']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="site_description" class="block text-white font-medium mb-2">Deskripsi Situs</label>
                        <textarea id="site_description" name="site_description" rows="3" class="w-full px-4 py-2 bg-[#e9e3dc] border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300"><?php echo htmlspecialchars($current_settings['site_description']); ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="phone_number" class="block text-white font-medium mb-2">Nomor Telepon</label>
                            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($current_settings['phone_number']); ?>" class="w-full px-4 py-2 bg-[#e9e3dc] border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300">
                        </div>
                        
                        <div>
                            <label for="address" class="block text-white font-medium mb-2">Alamat</label>
                            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($current_settings['address']); ?>" class="w-full px-4 py-2 bg-[#e9e3dc] border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="site_logo" class="block text-white font-medium mb-2">Logo Situs</label>
                        <div class="flex items-center">
                            <img src="../assets/images/<?php echo htmlspecialchars($current_settings['site_logo']); ?>" alt="Current Logo" class="h-16 mr-4 rounded">
                            <input type="file" id="site_logo" name="site_logo" accept="image/jpeg,image/png,image/gif" class="px-4 py-2 bg-[#e9e3dc] border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['site_logo']) ? 'border-red-500' : ''; ?>">
                        </div>
                        <p class="text-sm text-gray-200 mt-1">Ukuran rekomendasi: 200x50 piksel untuk logo, 32x32 piksel untuk favicon (jika digunakan).</p>
                        <?php if (isset($errors['site_logo'])): ?>
                            <p class="text-red-600 text-sm mt-1"><?php echo $errors['site_logo']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-stone-500 text-white px-6 py-2 rounded-lg font-semibold hover:bg-stone-400 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .animate-slideInDown {
            animation: slideInDown 0.8s ease-out forwards;
        }
        .animate-pulse {
            animation: pulse 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }
    </style>
</body>
</html>