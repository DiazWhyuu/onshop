<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Jika user sudah login, redirect ke halaman yang sesuai
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../index.php");
    }
    exit();
}

// Cek apakah ini login admin
$is_admin_login = isset($_GET['admin']) && $_GET['admin'] == 1;

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi";
    } else {
        // Cek credentials di database
        $sql = "SELECT id, username, password, role, full_name FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                
                // Redirect berdasarkan role
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    // Jika admin mencoba login sebagai user, redirect ke halaman admin
                    if ($is_admin_login) {
                        header("Location: ../admin/dashboard.php");
                    } else {
                        header("Location: ../index.php");
                    }
                }
                exit();
            } else {
                $error = "Username atau password salah";
            }
        } else {
            $error = "Username atau password salah";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_admin_login ? 'Admin Login' : 'User Login'; ?> - UPVC Store</title>
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
            <h1 class="text-3xl font-extrabold text-stone-800 tracking-tight">
                <?php echo $is_admin_login ? 'Admin Login' : 'Selamat Datang Kembali'; ?>
            </h1>
            <p class="text-stone-600 mt-2 text-sm font-medium">
                <?php echo $is_admin_login ? 'Masuk ke Dashboard Admin' : 'Masuk untuk melanjutkan belanja'; ?>
            </p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-900 border-l-4 border-red-600 text-red-100 px-4 py-3 rounded-lg mb-6 animate-slide-in">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-900 border-l-4 border-green-600 text-green-100 px-4 py-3 rounded-lg mb-6 animate-slide-in">
                <i class="fas fa-check-circle mr-2"></i>
                <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-stone-700 font-semibold mb-2 flex items-center">
                    <i class="fas fa-user mr-2 text-stone-600"></i>Username
                </label>
                <input type="text" id="username" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                       class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400" 
                       required autofocus>
            </div>
            
            <div>
                <label for="password" class="block text-stone-700 font-semibold mb-2 flex items-center">
                    <i class="fas fa-lock mr-2 text-stone-600"></i>Password
                </label>
                <input type="password" id="password" name="password" 
                       class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-transparent transition-all duration-200 bg-white text-stone-800 placeholder-stone-400 hover:border-stone-400" 
                       required>
            </div>
            
            <button type="submit" 
                    class="w-full bg-stone-600 text-white py-3 px-4 rounded-lg hover:bg-stone-700 focus:outline-none focus:ring-4 focus:ring-stone-300 focus:ring-opacity-50 transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Masuk
            </button>
            
            <?php if (!$is_admin_login): ?>
                
                
                <div class="mt-4 text-center">
                    <p class="text-stone-600 text-sm">
                        Belum punya akun? 
                        <a href="register.php" class="text-stone-800 hover:text-stone-900 font-medium transition-colors duration-200">
                            Daftar disini
                        </a>
                    </p>
                </div>
            <?php endif; ?>
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