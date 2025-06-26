<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

if (!isAdmin()) {
    header("Location: ../user/login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $action = $_POST['action'];

    if ($action === 'approve' || $action === 'reject') {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $sql = "UPDATE testimonials SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $status, $id);
        
        if ($stmt->execute()) {
            $success_message = "Testimonial berhasil di" . ($action === 'approve' ? 'setujui' : 'tolak') . ".";
        } else {
            $error_message = "Gagal memperbarui status testimonial.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Testimonial - UPVC Store</title>
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
            <div class="px-4 py-3 bg-stone-500 text-white">
                <span class="flex items-center justify-between">
                    <span class="font-semibold">Testimonial</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </div>
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
            <h2 class="text-xl font-semibold text-white">Kelola Testimonial</h2>
            <div class="flex items-center space-x-4">
                <span class="text-gray-200">Halo, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="../logout.php" class="text-white hover:text-indigo-300 font-medium transition-all duration-300">Logout</a>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <?php if ($success_message): ?>
                <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded-lg mb-6 animate-pulse">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="bg-red-900 border border-red-700 text-red-300 px-4 py-3 rounded-lg mb-6 animate-pulse">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-stone-500 rounded-xl shadow-xl border border-gray-700 animate-fadeInUp">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Daftar Testimonial</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-[#e9e3dc]">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">ID</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Nama</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Rating</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Komentar</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Status</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Tanggal</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-800 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT id, full_name, rating, comment, status, created_at FROM testimonials ORDER BY created_at DESC";
                                $result = $conn->query($sql);
                                
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status_class = '';
                                        switch ($row['status']) {
                                            case 'pending':
                                                $status_class = 'bg-yellow-900 text-yellow-300';
                                                break;
                                            case 'approved':
                                                $status_class = 'bg-green-900 text-green-300';
                                                break;
                                            case 'rejected':
                                                $status_class = 'bg-red-900 text-red-300';
                                                break;
                                        }
                                        echo '<tr class="hover:bg-stone-400 transition-all duration-300">';
                                        echo '<td class="py-3 px-4 text-gray-800">#' . $row['id'] . '</td>';
                                        echo '<td class="py-3 px-4 text-gray-800">' . htmlspecialchars($row['full_name']) . '</td>';
                                        echo '<td class="py-3 px-4 text-gray-800">' . $row['rating'] . ' Bintang</td>';
                                        echo '<td class="py-3 px-4 text-gray-800">' . htmlspecialchars(substr($row['comment'], 0, 50)) . (strlen($row['comment']) > 50 ? '...' : '') . '</td>';
                                        echo '<td class="py-3 px-4"><span class="px-2 py-1 rounded-full text-xs ' . $status_class . '">' . ucfirst($row['status']) . '</span></td>';
                                        echo '<td class="py-3 px-4 text-gray-800">' . date('d M Y', strtotime($row['created_at'])) . '</td>';
                                        echo '<td class="py-3 px-4 text-right">';
                                        if ($row['status'] === 'pending') {
                                            echo '
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="id" value="' . $row['id'] . '">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="text-green-600 hover:text-green-800 mr-2">Setujui</button>
                                            </form>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="id" value="' . $row['id'] . '">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="text-red-600 hover:text-red-800">Tolak</button>
                                            </form>';
                                        }
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="py-4 text-center text-gray-800">Tidak ada testimonial</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
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