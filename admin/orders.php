<?php
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdmin();

// Handle order actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    
    switch ($_GET['action']) {
        case 'update_status':
            if (isset($_POST['status'])) {
                $status = $_POST['status'];
                $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $order_id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Status pesanan berhasil diperbarui";
                } else {
                    $_SESSION['error_message'] = "Gagal memperbarui status pesanan";
                }
                header("Location: orders.php");
                exit();
            }
            break;
            
        case 'delete':
            $conn->begin_transaction();
            try {
                // Hapus order items terlebih dahulu
                $stmt = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
                $stmt->bind_param("i", $order_id);
                $stmt->execute();
                
                // Kemudian hapus order
                $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
                $stmt->bind_param("i", $order_id);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Pesanan berhasil dihapus";
                } else {
                    throw new Exception("Gagal menghapus pesanan");
                }
                
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                $_SESSION['error_message'] = "Gagal menghapus pesanan: " . $e->getMessage();
            }
            header("Location: orders.php");
            exit();
            break;
    }
}

// Get all orders
$sql = "SELECT o.*, u.full_name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC";
$orders = $conn->query($sql);

// Get order details if viewing single order
$order_details = null;
if (isset($_GET['view'])) {
    $order_details = getOrderDetails((int)$_GET['view']);
    if (!$order_details) {
        header("Location: orders.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - UPVC Store</title>
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
            <div class="px-4 py-3 bg-stone-500 text-white">
                <span class="flex items-center justify-between">
                    <span class="font-semibold">Pesanan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </div>
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
            <h2 class="text-xl font-semibold text-white">Kelola Pesanan</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-200">Halo, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="../logout.php" class="text-white hover:text-indigo-300 font-medium transition-all duration-300">Logout</a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-8 animate-slideInDown">Kelola Pesanan</h1>
            
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
            
            <?php if (isset($_GET['view'])): ?>
                <!-- Single Order View -->
                <div class="bg-stone-500 rounded-xl shadow-xl p-6 mb-6 border border-gray-700 animate-fadeInUp">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-semibold text-white">Detail Pesanan #<?php echo $order_details['id']; ?></h2>
                            <p class="text-gray-200">Tanggal: <?php echo date('d F Y H:i', strtotime($order_details['created_at'])); ?></p>
                        </div>
                        <a href="orders.php" class="text-white hover:text-indigo-300 font-medium transition-all duration-300">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-[#e9e3dc] p-4 rounded-lg animate-fadeInUp animation-delay-200">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Informasi Pelanggan</h3>
                            <p class="mb-1 text-gray-800"><strong>Nama:</strong> <?php echo $order_details['full_name']; ?></p>
                            <p class="mb-1 text-gray-800"><strong>Email:</strong> <?php echo $order_details['email']; ?></p>
                            
                        </div>
                        
                        <div class="bg-[#e9e3dc] p-4 rounded-lg animate-fadeInUp animation-delay-400">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Informasi Pesanan</h3>
                            <p class="mb-1 text-gray-800"><strong>Status:</strong> 
                                <span class="<?php 
                                    echo $order_details['status'] == 'pending' ? 'bg-yellow-900 text-yellow-300' : 
                                         ($order_details['status'] == 'processing' ? 'bg-[#e9e3dc] text-stone-500' : 
                                         ($order_details['status'] == 'completed' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300'));
                                    ?> px-2 py-1 rounded-full text-sm">
                                    <?php echo ucfirst($order_details['status']); ?>
                                </span>
                            </p>
                            <p class="mb-1 text-gray-800"><strong>Metode Pembayaran:</strong> 
                                <?php 
                                echo $order_details['payment_method'] == 'bank_transfer' ? 'Transfer Bank' : 
                                     ($order_details['payment_method'] == 'cod' ? 'Cash on Delivery (COD)' : 
                                     'Pembayaran Digital');
                                ?>
                            </p>
                            <?php if ($order_details['payment_method'] == 'bank_transfer' && !empty($order_details['payment_proof'])): ?>
                                <p class="mb-1 text-gray-800"><strong>Bukti Transfer:</strong></p>
                                <?php if (pathinfo($order_details['payment_proof'], PATHINFO_EXTENSION) === 'pdf'): ?>
                                    <a href="../<?php echo htmlspecialchars($order_details['payment_proof']); ?>" target="_blank" class="text-stone-600 hover:text-stone-800 transition-all duration-300">Lihat Bukti Transfer (PDF)</a>
                                <?php else: ?>
                                    <img src="../<?php echo htmlspecialchars($order_details['payment_proof']); ?>" alt="Bukti Transfer" class="max-w-[400px] h-auto rounded-lg shadow-md mt-2">
                                <?php endif; ?>
                            <?php endif; ?>
                            <p class="mb-1 text-gray-800"><strong>Total:</strong> Rp <?php echo number_format($order_details['total_amount'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-white mb-2">Alamat Pengiriman</h3>
                        <div class="bg-[#e9e3dc] p-4 rounded-lg text-gray-800 animate-fadeInUp animation-delay-600">
                            <?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-white mb-4">Item Pesanan</h3>
                    <div class="overflow-x-auto animate-fadeInUp animation-delay-800">
                        <table class="min-w-full">
                            <thead class="bg-[#e9e3dc]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Harga</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-300">
                                <?php foreach ($order_details['items'] as $item): ?>
                                    <tr class="hover:bg-stone-400 transition-all duration-300">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <img src="../assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="h-12 w-12 object-cover rounded">
                                                <div class="ml-4">
                                                    <h3 class="text-gray-800"><?php echo $item['name']; ?></h3>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-800">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                        <td class="px-6 py-4 text-gray-800"><?php echo $item['quantity']; ?></td>
                                        <td class="px-6 py-4 text-gray-800">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="flex justify-end mt-6 animate-fadeInUp animation-delay-1000">
                        <div class="w-full max-w-xs">
                            <div class="flex justify-between mb-2 text-gray-800">
                                <span>Subtotal</span>
                                <span>Rp <?php echo number_format($order_details['total_amount'], 0, ',', '.'); ?></span>
                            </div>
                            
                            <div class="flex justify-between mb-2 text-gray-800">
                                <span>Pengiriman</span>
                                <span>Gratis</span>
                            </div>
                            
                            <div class="border-t border-gray-400 my-2"></div>
                            
                            <div class="flex justify-between text-lg font-semibold text-gray-800">
                                <span>Total</span>
                                <span>Rp <?php echo number_format($order_details['total_amount'], 0, ',', '.'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-8 animate-fadeInUp animation-delay-1200">
                        <h3 class="text-lg font-semibold text-white mb-2">Update Status Pesanan</h3>
                        <form method="POST" action="orders.php?action=update_status&id=<?php echo $order_details['id']; ?>" class="flex items-center">
                            <select name="status" class="px-4 py-2 bg-[#e9e3dc] border border-gray-300 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-stone-500 transition-all duration-300 mr-2">
                                <option value="pending" <?php echo $order_details['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order_details['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="completed" <?php echo $order_details['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $order_details['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="bg-stone-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-stone-400 transition-all duration-300 transform hover:scale-105">
                                Update
                            </button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <!-- All Orders List -->
                <div class="bg-stone-500 rounded-xl shadow-xl border border-gray-700 overflow-hidden animate-fadeInUp">
                    <table class="min-w-full divide-y divide-gray-600">
                        <thead class="bg-[#e9e3dc]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600">
                            <?php if ($orders->num_rows > 0): ?>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr class="hover:bg-stone-400 transition-all duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                                            <div class="text-sm font-medium">#
                                            <?php echo $order['id']; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-800">
                                            <?php echo $order['full_name']; ?>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                            <?php echo $order['email']; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                            Rp
                                            <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full
                                            <?php 
                                                    echo $order['status'] == 'pending' ? 'bg-yellow-900 text-yellow-300' : 
                                                         ($order['status'] == 'processing' ? 'bg-[#e9e3dc] text-stone-500' : 
                                                         ($order['status'] == 'completed' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300'));
                                                    ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="orders.php?view=<?php echo $order['id']; ?>" class="text-stone-600 hover:text-stone-800 mr-3 transition-all duration-300" aria-label="View order">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="orders.php?action=delete&id=<?php echo $order['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')" class="text-red-600 hover:text-red-800 transition-all duration-300" aria-label="Delete order">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-800">
                                        Tidak ada pesanan yang ditemukan
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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
        .animation-delay-1000 { animation-delay: 1s; }
        .animation-delay-1200 { animation-delay: 1.2s; }
    </style>
</body>
</html>