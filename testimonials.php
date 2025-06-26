<?php
require_once 'includes/header.php';
require_once 'includes/config.php';
?>

<div class="container mx-auto px-4 py-12">
    <h1 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800">Testimonial Pelanggan</h1>
    <div class="grid md:grid-cols-3 gap-8">
        <?php
        $sql = "SELECT full_name, rating, comment FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $delay = 200;
            while ($row = $result->fetch_assoc()) {
                $initial = strtoupper(substr($row['full_name'], 0, 1));
                echo '
                <div class="bg-stone-500 p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 animate-fadeInUp animation-delay-' . $delay . '">
                    <div class="flex items-center mb-4">
                        <div class="bg-[#e9e3dc] text-gray-800 w-10 h-10 rounded-full flex items-center justify-center font-bold">' . htmlspecialchars($initial) . '</div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-white">' . htmlspecialchars($row['full_name']) . '</h4>
                            <div class="flex text-yellow-400">';
                for ($i = 1; $i <= 5; $i++) {
                    $filled = ($i <= $row['rating']) ? 'fill="currentColor"' : 'fill="none" stroke="currentColor" stroke-width="2"';
                    echo '
                                <svg class="w-5 h-5" ' . $filled . ' viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3 .921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784 .57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81 .588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>';
                }
                echo '
                            </div>
                        </div>
                    </div>
                    <p class="text-white">' . htmlspecialchars($row['comment']) . '</p>
                </div>';
                $delay += 200;
            }
        } else {
            echo '<p class="text-center text-gray-300 col-span-3">Belum ada testimonial yang tersedia.</p>';
        }
        ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>