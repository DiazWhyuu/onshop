<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    
    // Validate inputs
    $errors = [];
    
    if (empty($username)) {
        $errors['username'] = "Username harus diisi";
    } elseif (strlen($username) < 4) {
        $errors['username'] = "Username minimal 4 karakter";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password harus diisi";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password minimal 6 karakter";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Password tidak cocok";
    }
    
    if (empty($email)) {
        $errors['email'] = "Email harus diisi";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email tidak valid";
    }
    
    if (empty($first_name)) {
        $errors['first_name'] = "Nama depan harus diisi";
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $errors['general'] = "Username atau email sudah terdaftar";
        }
    }
    
    // Register user if no errors
    if (empty($errors)) {
        if (registerUser($username, $password, $email, $first_name, $last_name)) {
            $_SESSION['success_message'] = "Pendaftaran berhasil! Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            $errors['general'] = "Terjadi kesalahan. Silakan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - UPVC Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Snowfall animation */
        .snowflake {
            position: absolute;
            color: #d6d3d1; /* stone-300 */
            font-size: 1em;
            font-family: Arial, sans-serif;
            text-shadow: 0 0 5px rgba(0,0,0,0.2);
            pointer-events: none;
            animation: fall linear forwards;
        }

        @keyframes fall {
            0% { transform: translateY(-10vh); opacity: 1; }
            100% { transform: translateY(100vh); opacity: 0.5; }
        }

        /* Slide-in animation for alerts */
        @layer utilities {
            .animate-slide-in {
                animation: slide-in 0.5s ease-out;
            }
            @keyframes slide-in {
                0% { transform: translateX(-20px); opacity: 0; }
                100% { transform: translateX(0); opacity: 1; }
            }
        }
    </style>
</head>
<body class="bg-[#e9e3dc] min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Snowfall Canvas -->
    <div id="snowfall" class="fixed inset-0 pointer-events-none"></div>

    <div class="relative bg-white bg-opacity-95 backdrop-blur-sm p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all hover:scale-[1.02] duration-300 border border-stone-400">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">Daftar Akun Baru</h1>
            <p class="text-stone-600 mt-2 text-sm font-medium">Isi form berikut untuk membuat akun</p>
        </div>
        
        <?php if (isset($errors['general'])): ?>
            <div class="bg-red-900 border-l-4 border-red-600 text-red-100 px-4 py-3 rounded-lg mb-6 animate-slide-in">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $errors['general']; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-stone-700 font-semibold mb-2 flex items-center">
                    <i class="fas fa-user mr-2 text-stone-600"></i>Username
                </label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                       class="w-full px-4 py-3 border <?php echo isset($errors['username']) ? 'border-red-500' : 'border-stone-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400" 
                       required>
                <?php if (isset($errors['username'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo $errors['username']; ?></p>
                <?php endif; ?>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-stone-700 font-semibold mb-2 flex items-center">
                        <i class="fas fa-user mr-2 text-stone-600"></i>Nama Depan
                    </label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" 
                           class="w-full px-4 py-3 border <?php echo isset($errors['first_name']) ? 'border-red-500' : 'border-stone-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400" 
                           required>
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="text-red-600 text-sm mt-1"><?php echo $errors['first_name']; ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="last_name" class="block text-stone-700 font-semibold mb-2 flex items-center">
                        <i class="fas fa-user mr-2 text-stone-600"></i>Nama Belakang
                    </label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" 
                           class="w-full px-4 py-3 border <?php echo isset($errors['last_name']) ? 'border-red-500' : 'border-stone-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400">
                </div>
            </div>
            
            <div>
                <label for="email" class="block text-stone-700 font-semibold mb-2 flex items-center">
                    <i class="fas fa-envelope mr-2 text-stone-600"></i>Email
                </label>
                <input type="email" id="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                       class="w-full px-4 py-3 border <?php echo isset($errors['email']) ? 'border-red-500' : 'border-stone-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400" 
                       required>
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo $errors['email']; ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="password" class="block text-stone-700 font-semibold mb-2 flex items-center">
                    <i class="fas fa-lock mr-2 text-stone-600"></i>Password
                </label>
                <input type="password" id="password" name="password" 
                       class="w-full px-4 py-3 border <?php echo isset($errors['password']) ? 'border-red-500' : 'border-stone-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400" 
                       required>
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo $errors['password']; ?></p>
                <?php endif; ?>
            </div>
            
            <div>
                <label for="confirm_password" class="block text-stone-700 font-semibold mb-2 flex items-center">
                    <i class="fas fa-lock mr-2 text-stone-600"></i>Konfirmasi Password
                </label>
                <input type="password" id="confirm_password" name="confirm_password" 
                       class="w-full px-4 py-3 border <?php echo isset($errors['confirm_password']) ? 'border-red-500' : 'border-stone-300'; ?> rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400" 
                       required>
                <?php if (isset($errors['confirm_password'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?php echo $errors['confirm_password']; ?></p>
                <?php endif; ?>
            </div>
            
            <button type="submit" 
                    class="w-full bg-stone-600 text-white py-3 px-4 rounded-lg hover:bg-stone-700 focus:outline-none focus:ring-4 focus:ring-stone-300 focus:ring-opacity-50 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i>Daftar
            </button>
            
            <div class="mt-4 text-center">
                <p class="text-stone-600 text-sm">
                    Sudah punya akun? 
                    <a href="login.php" class="text-stone-800 hover:text-stone-900 font-medium transition-colors duration-200">
                        Login disini
                    </a>
                </p>
            </div>
        </form>
    </div>

    <script>
        // Snowfall animation
        function createSnowflake() {
            const snowflake = document.createElement('div');
            snowflake.classList.add('snowflake');
            snowflake.innerHTML = '❄';
            snowflake.style.left = Math.random() * 100 + 'vw';
            snowflake.style.animationDuration = Math.random() * 5 + 5 + 's';
            snowflake.style.opacity = Math.random() * 0.5 + 0.3;
            snowflake.style.fontSize = Math.random() * 10 + 10 + 'px';
            
            document.getElementById('snowfall').appendChild(snowflake);
            
            setTimeout(() => {
                snowflake.remove();
            }, 10000);
        }

        // Create snowflakes at intervals
        setInterval(createSnowflake, 200);
    </script>
</body>
</html>