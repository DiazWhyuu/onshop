<?php
require_once 'auth.php';
require_once 'functions.php'; // Adjusted path

// Get settings from database
$current_settings = [];
$sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_title', 'site_logo')";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

// Set default values
$defaults = [
    'site_title' => 'UPVC Store',
    'site_logo' => 'logo.png'
];
$current_settings = array_merge($defaults, $current_settings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($current_settings['site_title']); ?> - Pintu & Jendela UPVC Berkualitas</title>
    <link rel="icon" type="image/x-icon" href="assets/images/<?php echo htmlspecialchars($current_settings['site_logo']); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-[#e9e3dc] font-sans">
    <!-- Navbar -->
    <nav class="sticky top-0 bg-[#e9e3dc] backdrop-blur-md text-black shadow-lg z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <!-- Logo -->
            <a href="index.php" class="flex items-center space-x-3 transition-transform hover:scale-105">
                <img src="assets/images/<?php echo htmlspecialchars($current_settings['site_logo']); ?>" alt="<?php echo htmlspecialchars($current_settings['site_title']); ?> Logo" class="h-12">
                <span class="text-2xl font-extrabold tracking-tight"><?php echo htmlspecialchars($current_settings['site_title']); ?></span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center space-x-8 text-black">
                <a href="index.php" class="text-lg font-light hover:text-gray-800 relative group">
                    Beranda
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gray-800 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="products.php" class="text-lg font-light hover:text-gray-800 relative group">
                    Produk
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gray-800 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="about.php" class="text-lg font-light hover:text-gray-800 relative group">
                    Tentang Kami
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gray-800 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="testimonials.php" class="text-lg font-light hover:text-gray-800 relative group">
                    Testimonial
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gray-800 transition-all duration-300 group-hover:w-full"></span>
                </a>
                <a href="contact.php" class="text-lg font-light hover:text-gray-800 relative group">
                    Kontak
                    <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gray-800 transition-all duration-300 group-hover:w-full"></span>
                </a>
            </div>

            <!-- User/Auth Dropdown -->
            <div class="relative">
                <?php if (isLoggedIn()): ?>
                    <button id="user-menu" class="flex items-center space-x-2 text-lg font-medium hover:text-gray-700 focus:outline-none transition-colors">
                        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-gray-800 rounded-xl shadow-xl py-2 z-50 border border-gray-600">
                        <?php if (isAdmin()): ?>
                            <a href="admin/dashboard.php" class="block px-4 py-3 text-white hover:bg-gray-700 hover:text-indigo-200 transition-colors">Dashboard Admin</a>
                            <a href="index.php" class="block px-4 py-3 text-white hover:bg-gray-700 hover:text-indigo-200 transition-colors">Kunjungi Website</a>
                        <?php else: ?>
                            <a href="user/profile.php" class="block px-4 py-3 text-white hover:bg-gray-700 hover:text-indigo-200 transition-colors">Profil Saya</a>
                            <a href="cart.php" class="block px-4 py-3 text-white hover:bg-gray-700 hover:text-indigo-200 transition-colors">Keranjang Belanja</a>
                            <a href="transactions.php" class="block px-4 py-3 text-white hover:bg-gray-700 hover:text-indigo-200 transition-colors">Transaksi</a>
                            <a href="user/add_testimonial.php" class="block px-4 py-3 text-white hover:bg-gray-700 hover:text-indigo-200 transition-colors">Tambah Testimonial</a>
                        <?php endif; ?>
                        <a href="logout.php" class="block px-4 py-3 text-white hover:bg-gray-700 hover:text-indigo-200 transition-colors">Logout</a>
                    </div>
                <?php else: ?>
                    <button id="auth-menu" class="flex items-center space-x-2 text-lg font-medium hover:text-indigo-200 focus:outline-none transition-colors">
                        <span>Login</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="auth-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl py-2 z-50 border border-gray-100">
                        <a href="user/login.php" class="block px-4 py-3 text-gray-800 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Login User</a>
                        <a href="user/register.php" class="block px-4 py-3 text-gray-800 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Register User</a>
                        <a href="user/login.php?admin=1" class="block px-4 py-3 text-gray-800 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">Login Admin</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="lg:hidden focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-gray-800">
            <div class="container mx-auto px-4 py-4 flex flex-col space-y-4">
                <a href="index.php" class="text-lg font-medium text-white hover:text-indigo-200 py-2">Beranda</a>
                <a href="products.php" class="text-lg font-medium text-white hover:text-indigo-200 py-2">Produk</a>
                <a href="about.php" class="text-lg font-medium text-white hover:text-indigo-200 py-2">Tentang Kami</a>
                <a href="testimonials.php" class="text-lg font-medium text-white hover:text-indigo-200 py-2">Testimonial</a>
                <a href="contact.php" class="text-lg font-medium text-white hover:text-indigo-200 py-2">Kontak</a>
            </div>
        </div>
    </nav>

    <script>
        // Toggle dropdown and mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            // User/Auth Dropdown
            const userMenu = document.getElementById('user-menu');
            const authMenu = document.getElementById('auth-menu');
            const userDropdown = document.getElementById('user-dropdown');
            const authDropdown = document.getElementById('auth-dropdown');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (userMenu && userDropdown) {
                userMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('hidden');
                    if (authDropdown) authDropdown.classList.add('hidden');
                });
            }

            if (authMenu && authDropdown) {
                authMenu.addEventListener('click', function(e) {
                    e.stopPropagation();
                    authDropdown.classList.toggle('hidden');
                    if (userDropdown) userDropdown.classList.add('hidden');
                });
            }

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Close dropdowns and mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                if (userDropdown && !userDropdown.contains(event.target) && userMenu && !userMenu.contains(event.target)) {
                    userDropdown.classList.add('hidden');
                }
                if (authDropdown && !authDropdown.contains(event.target) && authMenu && !authMenu.contains(event.target)) {
                    authDropdown.classList.add('hidden');
                }
            });
        });
    </script>