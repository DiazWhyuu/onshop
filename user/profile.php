<?php
require_once '../includes/header-user-profile.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update profile if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    
    // Update password if provided
    $password_update = '';
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_update = ", password = ?";
    }
    
    $sql = "UPDATE users SET full_name = ?, email = ?, address = ?, phone = ? $password_update WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!empty($_POST['password'])) {
        $stmt->bind_param("sssssi", $full_name, $email, $address, $phone, $password, $user_id);
    } else {
        $stmt->bind_param("ssssi", $full_name, $email, $address, $phone, $user_id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['full_name'] = $full_name;
        $success = "Profil berhasil diperbarui!";
        // Refresh user data
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    } else {
        $error = "Gagal memperbarui profil: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-stone-500 text-gray-800 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold mb-8 text-gray-800 animate-fadeIn">Profil Saya</h1>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-800 border border-green-600 text-green-100 px-4 py-3 rounded-lg mb-6 animate-fadeIn">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-800 border border-red-600 text-red-100 px-4 py-3 rounded-lg mb-6 animate-fadeIn">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="bg-stone-500 rounded-lg shadow-xl p-6 transform transition-all hover:shadow-2xl">
                <form method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="username" class="block text-white mb-2">Username</label>
                            <input type="text" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" readonly>
                        </div>
                        
                        <div>
                            <label for="full_name" class="block text-white mb-2">Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-white mb-2">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" required>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-white mb-2">Telepon</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" 
                                   class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label for="address" class="block text-white mb-2">Alamat</label>
                            <textarea id="address" name="address" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        
                        <div>
                            <label for="password" class="block text-white mb-2">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" id="password" name="password" 
                                   class="w-full px-4 py-2 border border-gray-600 rounded-lg bg-white text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                        </div>
                    </div>
                    
                    <div class="mt-8 flex justify-end">
                        <button type="submit" 
                                class="bg-[#e9e3dc] text-gray-800 px-6 py-2 rounded-lg hover:bg-stone-300 transition-all transform hover:scale-105">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require_once '../includes/footer.php'; ?>
</body>
</html>