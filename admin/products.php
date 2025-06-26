<?php
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $id = (int)$_GET['id'];
                $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Produk berhasil dihapus";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus produk";
                }
                header("Location: products.php");
                exit();
            }
            break;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $type = $_POST['type'];
    $stock = (int)$_POST['stock'];
    $size = trim($_POST['size']);
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    
    // Validasi
    $errors = [];
    if (empty($name)) $errors['name'] = "Nama produk harus diisi";
    if (empty($description)) $errors['description'] = "Deskripsi harus diisi";
    if ($price <= 0) $errors['price'] = "Harga harus lebih dari 0";
    if (!in_array($type, ['pintu', 'jendela'])) $errors['type'] = "Tipe produk tidak valid";
    if ($stock < 0) $errors['stock'] = "Stok tidak boleh negatif";
    
    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors['image'] = "Hanya file JPG, PNG, atau GIF yang diizinkan";
        } else {
            $upload_dir = '../assets/images/';
            $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid('product_') . '.' . $file_ext;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image)) {
                $errors['image'] = "Gagal mengunggah gambar";
            }
        }
    } elseif ($id && empty($_FILES['image']['name'])) {
        // Jika edit produk dan tidak upload gambar baru, gunakan gambar lama
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();
        $image = $existing['image'];
    }
    
    if (empty($errors)) {
        if ($id) {
            // Update produk
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, type = ?, stock = ?, image = ?, size = ? WHERE id = ?");
            $stmt->bind_param("ssdsissi", $name, $description, $price, $type, $stock, $image, $size, $id);
        } else {
            // Tambah produk baru
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, type, stock, image, size) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsiss", $name, $description, $price, $type, $stock, $image, $size);
        }
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = $id ? "Produk berhasil diperbarui" : "Produk berhasil ditambahkan";
            header("Location: products.php");
            exit();
        } else {
            $errors['general'] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}

// Get all products
$products = getAllProducts();

// Get product for edit
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_product = getProductById((int)$_GET['edit']);
    if (!$edit_product) {
        header("Location: products.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - UPVC Store</title>
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
            <div class="px-4 py-3 bg-stone-500 text-white">
                <span class="flex items-center justify-between">
                    <span class="font-semibold">Produk</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </div>
            <a href="orders.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Pesanan</a>
            <a href="testimonials.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Testimonial</a>
            <a href="settings.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Pengaturan</a>
            <a href="../index.php" class="block px-4 py-3 text-white bg-stone-500 hover:bg-stone-400 mt-4 mx-4 rounded-lg font-semibold transition-all duration-300 transform hover:scale-105">
                <i class="fas fa-external-link-alt mr-2"></i> Kunjungi Website
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 overflow-auto">
        <!-- Topbar -->
        <div class="bg-stone-500 border-b border-gray-700 p-4 flex justify-between items-center animate-slideInDown">
            <h2 class="text-xl font-semibold text-white">Kelola Produk</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-200">Halo, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="../logout.php" class="text-white hover:text-indigo-300 font-medium transition-all duration-300">Logout</a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 animate-slideInDown">
                <h1 class="text-3xl font-bold text-gray-800">Kelola Produk</h1>
                <button onclick="document.getElementById('productModal').classList.remove('hidden')" class="bg-stone-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-stone-400 transition-all duration-300 transform hover:scale-105 mt-4 md:mt-0">
                    <i class="fas fa-plus mr-2"></i> Tambah Produk
                </button>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6 animate-pulse">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6 animate-pulse">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="bg-stone-500 rounded-xl shadow-xl border border-gray-700 overflow-hidden animate-fadeInUp">
                <table class="min-w-full divide-y divide-gray-600">
                    <thead class="bg-[#e9e3dc]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Gambar</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Stok</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        <?php if ($products->num_rows > 0): ?>
                            <?php while ($product = $products->fetch_assoc()): ?>
                                <tr class="hover:bg-stone-400 transition-all duration-300">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <img src="../assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="h-12 w-12 object-cover rounded">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-white"><?php echo $product['name']; ?></div>
                                        <div class="text-sm text-gray-200 truncate max-w-xs"><?php echo $product['description']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full <?php echo $product['type'] == 'pintu' ? 'bg-stone-700 text-white' : 'bg-stone-600 text-white'; ?>">
                                            <?php echo ucfirst($product['type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <?php echo !empty($product['size']) ? htmlspecialchars($product['size']) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        <?php echo $product['stock']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="products.php?edit=<?php echo $product['id']; ?>" class="text-white hover:text-gray-800 mr-3 transition-all duration-300" aria-label="Edit product">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="text-white hover:text-gray-800 transition-all duration-300" aria-label="Delete product">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-white">
                                    Tidak ada produk yang tersedia
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm overflow-y-auto h-full w-full animate-fadeIn">
        <div class="relative top-20 mx-auto p-6 border w-full max-w-2xl shadow-xl rounded-xl bg-[#e9e3dc] border-gray-800">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    <?php echo $edit_product ? 'Edit Produk' : 'Tambah Produk Baru'; ?>
                </h3>
                <button onclick="document.getElementById('productModal').classList.add('hidden')" class="text-gray-600 hover:text-gray-800 transition-all duration-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="POST" action="products.php" enctype="multipart/form-data">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                <?php endif; ?>
                
                <?php if (isset($errors['general'])): ?>
                    <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-4 animate-pulse">
                        <?php echo $errors['general']; ?>
                    </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-800 font-medium mb-2">Nama Produk</label>
                    <input type="text" id="name" name="name" value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : (isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''); ?>" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['name']) ? 'border-red-500' : ''; ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo $errors['name']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-gray-800 font-medium mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['description']) ? 'border-red-500' : ''; ?>" required><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : (isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''); ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo $errors['description']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="price" class="block text-gray-800 font-medium mb-2">Harga</label>
                        <input type="number" id="price" name="price" min="0" step="1000" value="<?php echo $edit_product ? $edit_product['price'] : (isset($_POST['price']) ? $_POST['price'] : ''); ?>" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['price']) ? 'border-red-500' : ''; ?>" required>
                        <?php if (isset($errors['price'])): ?>
                            <p class="text-red-600 text-sm mt-1"><?php echo $errors['price']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="type" class="block text-gray-800 font-medium mb-2">Tipe</label>
                        <select id="type" name="type" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['type']) ? 'border-red-500' : ''; ?>" required>
                            <option value="pintu" <?php echo ($edit_product && $edit_product['type'] == 'pintu') || (isset($_POST['type']) && $_POST['type'] == 'pintu') ? 'selected' : ''; ?>>Pintu</option>
                            <option value="jendela" <?php echo ($edit_product && $edit_product['type'] == 'jendela') || (isset($_POST['type']) && $_POST['type'] == 'jendela') ? 'selected' : ''; ?>>Jendela</option>
                        </select>
                        <?php if (isset($errors['type'])): ?>
                            <p class="text-red-600 text-sm mt-1"><?php echo $errors['type']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <label for="stock" class="block text-gray-800 font-medium mb-2">Stok</label>
                        <input type="number" id="stock" name="stock" min="0" value="<?php echo $edit_product ? $edit_product['stock'] : (isset($_POST['stock']) ? $_POST['stock'] : ''); ?>" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['stock']) ? 'border-red-500' : ''; ?>" required>
                        <?php if (isset($errors['stock'])): ?>
                            <p class="text-red-600 text-sm mt-1"><?php echo $errors['stock']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="size" class="block text-gray-800 font-medium mb-2">Ukuran (contoh: 200x100 cm)</label>
                    <input type="text" id="size" name="size" value="<?php echo $edit_product ? htmlspecialchars($edit_product['size']) : (isset($_POST['size']) ? htmlspecialchars($_POST['size']) : ''); ?>" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300">
                </div>
                
                <div class="mb-4">
                    <label for="image" class="block text-gray-800 font-medium mb-2">Gambar Produk</label>
                    <?php if ($edit_product && $edit_product['image']): ?>
                        <img src="../assets/images/<?php echo $edit_product['image']; ?>" alt="Current Image" class="h-24 mb-2 rounded">
                        <p class="text-sm text-gray-600 mb-2">Biarkan kosong jika tidak ingin mengubah gambar</p>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 <?php echo isset($errors['image']) ? 'border-red-500' : ''; ?>" <?php echo !$edit_product ? 'required' : ''; ?>>
                    <?php if (isset($errors['image'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo $errors['image']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" onclick="document.getElementById('productModal').classList.add('hidden')" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg mr-2 hover:bg-gray-400 transition-all duration-300 transform hover:scale-105">
                        Batal
                    </button>
                    <button type="submit" class="bg-stone-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-stone-400 transition-all duration-300 transform hover:scale-105">
                        <?php echo $edit_product ? 'Update Produk' : 'Tambah Produk'; ?>
                    </button>
                </div>
            </form>
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
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .animate-slideInDown {
            animation: slideInDown 0.8s ease-out forwards;
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>

    <script>
        // Show modal if there are errors or if editing
        <?php if (isset($errors) || isset($_GET['edit'])): ?>
            document.getElementById('productModal').classList.remove('hidden');
        <?php endif; ?>
    </script>
</body>
</html>