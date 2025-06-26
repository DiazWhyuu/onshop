<?php
require_once '../includes/auth.php';
require_once '../includes/config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $user_id = $_SESSION['user_id'];
    $full_name = $_SESSION['full_name'];

    // Validation
    if ($rating < 1 || $rating > 5) {
        $error_message = 'Rating harus antara 1 dan 5 bintang.';
    } elseif (empty($comment)) {
        $error_message = 'Komentar tidak boleh kosong.';
    } else {
        // Insert testimonial
        $sql = "INSERT INTO testimonials (user_id, full_name, rating, comment, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('isis', $user_id, $full_name, $rating, $comment);
        
        if ($stmt->execute()) {
            $success_message = 'Testimonial berhasil dikirim dan menunggu persetujuan admin.';
        } else {
            $error_message = 'Gagal mengirim testimonial. Silakan coba lagi.';
        }
        $stmt->close();
    }
}
?>

<?php require_once '../includes/header-user-profile.php'; ?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8 text-center">Tambah Testimonial</h1>
    
    <?php if ($success_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            <p><?php echo htmlspecialchars($success_message); ?></p>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <p><?php echo htmlspecialchars($error_message); ?></p>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-lg">
        <div class="mb-6">
            <label for="rating" class="block text-gray-700 font-semibold mb-2">Rating (1-5 Bintang)</label>
            <select id="rating" name="rating" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                <option value="">Pilih rating</option>
                <option value="1">1 Bintang</option>
                <option value="2">2 Bintang</option>
                <option value="3">3 Bintang</option>
                <option value="4">4 Bintang</option>
                <option value="5">5 Bintang</option>
            </select>
        </div>
        <div class="mb-6">
            <label for="comment" class="block text-gray-700 font-semibold mb-2">Komentar</label>
            <textarea id="comment" name="comment" rows="5" class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" required></textarea>
        </div>
        <button type="submit" class="w-full bg-stone-500 text-white py-3 rounded-full font-semibold hover:text-gray-800 hover:bg-stone-300 transition duration-300">Kirim Testimonial</button>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>