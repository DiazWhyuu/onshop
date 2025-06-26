<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

if (!isLoggedIn() || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['id'];
$order = getOrderDetails($order_id);

// Pastikan order ini milik user yang login (kecuali admin)
if (!$order || ($order['user_id'] != $_SESSION['user_id'] && !isAdmin())) {
    header("Location: index.php");
    exit();
}

// Format tanggal
$order_date = date('d F Y H:i', strtotime($order['created_at']));
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-8">
            <div class="bg-green-100 text-green-800 p-4 rounded-full inline-flex items-center justify-center mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold mb-2">Pesanan Berhasil Diproses!</h1>
            <p class="text-gray-600">Terima kasih telah berbelanja di UPVC Store</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row justify-between mb-6">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-lg font-semibold mb-2">Detail Pesanan</h2>
                    <p class="text-gray-600">No. Pesanan: <span class="font-medium">#<?php echo $order['id']; ?></span></p>
                    <p class="text-gray-600">Tanggal: <span class="font-medium"><?php echo $order_date; ?></span></p>
                    <p class="text-gray-600">Status: 
                        <span class="<?php 
                            echo $order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                 ($order['status'] == 'processing' ? 'bg-blue-100 text-blue-800' : 
                                 ($order['status'] == 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'));
                            ?> px-2 py-1 rounded-full text-sm">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold mb-2">Metode Pembayaran</h2>
                    <p class="text-gray-600">
                        <?php 
                        echo $order['payment_method'] == 'bank_transfer' ? 'Transfer Bank' : 
                             ($order['payment_method'] == 'cod' ? 'Cash on Delivery (COD)' : 
                             'Pembayaran Digital');
                        ?>
                    </p>
                    <?php if ($order['payment_method'] == 'bank_transfer'): ?>
                        <p class="text-sm text-gray-500 mt-1">
                            Silakan transfer ke rekening BCA 1234567890 a.n. UPVC Store
                        </p>
                        <?php if (!empty($order['payment_proof'])): ?>
                            <div class="mt-4">
                                <h3 class="text-md font-medium mb-2">Bukti Transfer</h3>
                                <?php if (pathinfo($order['payment_proof'], PATHINFO_EXTENSION) === 'pdf'): ?>
                                    <a href="<?php echo htmlspecialchars($order['payment_proof']); ?>" target="_blank" class="text-blue-600 hover:underline">Lihat Bukti Transfer (PDF)</a>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($order['payment_proof']); ?>" alt="Bukti Transfer" class="max-w-[400px] h-auto rounded-lg shadow-md">
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
            </div>
            
            <div class="border-t border-gray-200 my-4"></div>
            
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-2">Alamat Pengiriman</h2>
                <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            </div>
            
            <div class="border-t border-gray-200 my-4"></div>
            
            <h2 class="text-lg font-semibold mb-4">Rincian Pesanan</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="h-12 w-12 object-cover rounded">
                                        <div class="ml-4">
                                            <h3 class="text-gray-800"><?php echo $item['name']; ?></h3>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td class="px-6 py-4"><?php echo $item['quantity']; ?></td>
                                <td class="px-6 py-4">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="border-t border-gray-200 my-4"></div>
            
            <div class="flex justify-end">
                <div class="w-full max-w-xs">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Pengiriman</span>
                        <span>Gratis</span>
                    </div>
                    
                    <div class="border-t border-gray-200 my-2"></div>
                    
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total</span>
                        <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <a href="products.php" class="inline-block bg-blue-800 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 mr-4">
                <i class="fas fa-shopping-bag mr-2"></i> Lanjut Belanja
            </a>
            <a href="transactions.php" class="inline-block bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                <i class="fas fa-user mr-2"></i> Lihat Pesanan Saya
            </a>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>