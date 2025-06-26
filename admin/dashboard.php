<?php
require_once '../includes/auth.php';

if (!isAdmin()) {
    header("Location: ../user/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UPVC Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-[#e9e3dc] text-white min-h-screen flex">
    <!-- Sidebar -->
    <div class="bg-[#e9e3dc] text-gray-800 w-64 flex-shrink-0 border-r border-gray-800">
        <div class="p-6">
            <h1 class="text-2xl font-bold tracking-tight animate-slideInDown">UPVC Store Admin</h1>
        </div>
        <nav class="mt-6">
            <div class="px-4 py-3 bg-[#e9e3dc] text-gray-800">
                <span class="flex items-center justify-between">
                    <span class="font-semibold">Dashboard</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </div>
            <a href="products.php" class="block px-4 py-3 text-gray-800 hover:bg-stone-500 hover:text-white transition-all duration-300 transform hover:-translate-y-1">Produk</a>
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
            <h2 class="text-xl font-semibold text-white">Dashboard</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-200">Halo, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="../logout.php" class="text-white hover:text-indigo-300 font-medium transition-all duration-300">Logout</a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Products -->
                <div class="bg-stone-500 p-6 rounded-xl shadow-xl border border-gray-700 transform transition-all duration-300 hover:scale-105 animate-fadeInUp">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-200 font-medium">Total Produk</h3>
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM products";
                            $result = $conn->query($sql);
                            $total_products = $result->fetch_assoc()['total'];
                            ?>
                            <p class="text-3xl font-bold text-white"><?php echo $total_products; ?></p>
                        </div>
                        <div class="bg-[#e9e3dc] p-3 rounded-full">
                            <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <a href="products.php" class="mt-4 inline-block text-white hover:text-indigo-300 font-medium transition-all duration-300">Lihat semua produk</a>
                </div>

                <!-- Total Orders -->
                <div class="bg-stone-500 p-6 rounded-xl shadow-xl border border-gray-700 transform transition-all duration-300 hover:scale-105 animate-fadeInUp animation-delay-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-200 font-medium">Total Pesanan</h3>
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM orders";
                            $result = $conn->query($sql);
                            $total_orders = $result->fetch_assoc()['total'];
                            ?>
                            <p class="text-3xl font-bold text-white"><?php echo $total_orders; ?></p>
                        </div>
                        <div class="bg-[#e9e3dc] p-3 rounded-full">
                            <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <a href="orders.php" class="mt-4 inline-block text-white hover:text-indigo-300 font-medium transition-all duration-300">Lihat semua pesanan</a>
                </div>

                <!-- Total Users -->
                <div class="bg-stone-500 p-6 rounded-xl shadow-xl border border-gray-700 transform transition-all duration-300 hover:scale-105 animate-fadeInUp animation-delay-400">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-200 font-medium">Total Pengguna</h3>
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
                            $result = $conn->query($sql);
                            $total_users = $result->fetch_assoc()['total'];
                            ?>
                            <p class="text-3xl font-bold text-white"><?php echo $total_users; ?></p>
                        </div>
                        <div class="bg-[#e9e3dc] p-3 rounded-full">
                            <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <a href="#" class="mt-4 inline-block text-white hover:text-indigo-300 font-medium transition-all duration-300">Lihat semua pengguna</a>
                </div>

                <!-- Total Pending Testimonials -->
                <div class="bg-stone-500 p-6 rounded-xl shadow-xl border border-gray-700 transform transition-all duration-300 hover:scale-105 animate-fadeInUp animation-delay-600">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-gray-200 font-medium">Testimonial Menunggu Persetujuan</h3>
                            <?php
                            $sql = "SELECT COUNT(*) as total FROM testimonials WHERE status = 'pending'";
                            $result = $conn->query($sql);
                            $total_pending_testimonials = $result->fetch_assoc()['total'];
                            ?>
                            <p class="text-3xl font-bold text-white"><?php echo $total_pending_testimonials; ?></p>
                        </div>
                        <div class="bg-[#e9e3dc] p-3 rounded-full">
                            <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3 .922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783 .57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81 .588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                    </div>
                    <a href="testimonials.php" class="mt-4 inline-block text-white hover:text-indigo-300 font-medium transition-all duration-300">Kelola Testimonial</a>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-stone-500 rounded-xl shadow-xl border border-gray-700 animate-fadeInUp animation-delay-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Pesanan Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-[#e9e3dc]">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">ID Pesanan</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Nama Pelanggan</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 Tiempo">Price</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Tanggal</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT o.id, u.full_name, o.total_amount, o.status, o.created_at 
                                        FROM orders o 
                                        JOIN users u ON o.user_id = u.id 
                                        ORDER BY o.created_at DESC 
                                        LIMIT 5";
                                $result = $conn->query($sql);
                                
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $status_class = '';
                                        switch ($row['status']) {
                                            case 'pending':
                                                $status_class = 'bg-yellow-900 text-yellow-300';
                                                break;
                                            case 'processing':
                                                $status_class = 'bg-[#e9e3dc] text-indigo-300';
                                                break;
                                            case 'completed':
                                                $status_class = 'bg-green-900 text-green-300';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'bg-red-900 text-red-300';
                                                break;
                                        }
                                        
                                        echo '<tr class="hover:bg-stone-400 transition-all duration-300">';
                                        echo '<td class="py-3 px-4 text-gray-200">#' . $row['id'] . '</td>';
                                        echo '<td class="py-3 px-4 text-gray-200">' . htmlspecialchars($row['full_name']) . '</td>';
                                        echo '<td class="py-3 px-4 text-gray-200">Rp ' . number_format($row['total_amount'], 0, ',', '.') . '</td>';
                                        echo '<td class="py-3 px-4"><span class="px-2 py-1 rounded-full text-xs ' . $status_class . '">' . ucfirst($row['status']) . '</span></td>';
                                        echo '<td class="py-3 px-4 text-gray-200">' . date('d M Y', strtotime($row['created_at'])) . '</td>';
                                        echo '<td class="py-3 px-4 text-right"><a href="orders.php?action=view&id=' . $row['id'] . '" class="text-indigo-300 hover:text-indigo-200 transition-all duration-300">Detail</a></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="py-4 text-center text-gray-200">Tidak ada pesanan</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="orders.php" class="text-white hover:text-indigo-300 font-medium transition-all duration-300">Lihat semua pesanan →</a>
                    </div>
                </div>
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
        .animation-delay-200 { animation-delay: 0.2s; }
        .animation-delay-400 { animation-delay: 0.4s; }
        .animation-delay-600 { animation-delay: 0.6s; }
        .animation-delay-800 { animation-delay: 0.8s; }
    </style>
</body>
</html>