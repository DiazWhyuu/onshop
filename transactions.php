<?php
ob_start();
require_once 'includes/header.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Handle order cancellation
if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['id'])) {
    $order_id = (int)$_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Check if order belongs to user and is not completed
    $sql = "SELECT status FROM orders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if ($order && $order['status'] === 'pending') {
        $sql = "UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $user_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pesanan berhasil dibatalkan";
        } else {
            $_SESSION['error_message'] = "Gagal membatalkan pesanan: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Pesanan tidak dapat dibatalkan";
    }
    header("Location: transactions.php");
    exit();
}

// Pastikan tidak ada output sebelum ini
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi</title>
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
<body class="bg-stone-500 text-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl fontshe font-bold mb-8 text-gray-800 animate-fadeIn">Riwayat Transaksi</h1>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-800 border border-green-600 text-green-100 px-4 py-3 rounded-lg mb-6 animate-fadeIn">
                    <?php 
                    echo $_SESSION['success_message']; 
                    unset($_SESSION['success_message']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="bg-red-800 border border-red-600 text-red-100 px-4 py-3 rounded-lg mb-6 animate-fadeIn">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="bg-stone-500 rounded-lg shadow-xl p-6 transform transition-all hover:shadow-2xl">
                <?php 
                // Get user orders
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT id, total_amount, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $orders = $stmt->get_result();
                
                if ($orders->num_rows > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-stone-500">
                            <thead>
                                <tr class="bg-stone-400">
                                    <th class="py-2 px-4 text-left text-sm font-semibold text-white uppercase tracking-wider">ID Pesanan</th>
                                    <th class="py-2 px-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Total</th>
                                    <th class="py-2 px-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Status</th>
                                    <th class="py-2 px-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Tanggal</th>
                                    <th class="py-2 px-4 text-right text-sm font-semibold text-white uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-600">
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-700 text-white transition-all animate-fadeIn">
                                        <td class="py-2 px-4">#<?php echo $order['id']; ?></td>
                                        <td class="py-2 px-4">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                        <td class="py-2 px-4">
                                            <?php 
                                            $status_class = [
                                                'pending' => 'bg-yellow-900 text-yellow-300',
                                                'processing' => 'bg-blue-900 text-blue-300',
                                                'completed' => 'bg-green-900 text-green-300',
                                                'cancelled' => 'bg-red-900 text-red-300'
                                            ];
                                            ?>
                                            <span class="px-2 py-1 rounded-full text-xs <?php echo $status_class[$order['status']]; ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="py-2 px-4"><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                        <td class="py-2 px-4 text-right space-x-2">
                                            <a href="order-detail.php?id=<?php echo $order['id']; ?>" 
                                               class="text-indigo-400 hover:text-indigo-700 transition-all">Detail</a>
                                            <?php if ($order['status'] === 'pending'): ?>
                                                <a href="transactions.php?action=cancel&id=<?php echo $order['id']; ?>" 
                                                   onclick="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')" 
                                                   class="text-red-400 hover:text-red-700 transition-all">Batalkan</a>
                                            <?php else: ?>
                                                <span class="text-white cursor-not-allowed">Batalkan</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400">Anda belum memiliki pesanan.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>