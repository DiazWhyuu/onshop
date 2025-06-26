<?php
require_once 'includes/header.php';
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pintu & Jendela UPVC</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        @keyframes snowFall {
            0% {
                transform: translateY(-10vh);
                opacity: 1;
            }
            100% {
                transform: translateY(100vh);
                opacity: 0.7;
            }
        }
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        .animate-pulse-slow {
            animation: pulse 2s ease-in-out infinite;
        }
        .snowflake {
            position: absolute;
            width: 8px;
            height: 8px;
            background: white;
            border-radius: 50%;
            opacity: 0.8;
            pointer-events: none;
            animation: snowFall linear infinite;
        }
        .snowflake:nth-child(1) { left: 5%; animation-duration: 5s; animation-delay: 0s; }
        .snowflake:nth-child(2) { left: 10%; animation-duration: 6s; animation-delay: 0.5s; }
        .snowflake:nth-child(3) { left: 15%; animation-duration: 5.5s; animation-delay: 1s; }
        .snowflake:nth-child(4) { left: 20%; animation-duration: 7s; animation-delay: 1.5s; }
        .snowflake:nth-child(5) { left: 25%; animation-duration: 6.5s; animation-delay: 2s; }
        .snowflake:nth-child(6) { left: 30%; animation-duration: 5s; animation-delay: 0.2s; }
        .snowflake:nth-child(7) { left: 35%; animation-duration: 6s; animation-delay: 0.7s; }
        .snowflake:nth-child(8) { left: 40%; animation-duration: 7.5s; animation-delay: 1.2s; }
        .snowflake:nth-child(9) { left: 45%; animation-duration: 5.5s; animation-delay: 1.7s; }
        .snowflake:nth-child(10) { left: 50%; animation-duration: 6s; animation-delay: 0.3s; }
        .snowflake:nth-child(11) { left: 55%; animation-duration: 7s; animation-delay: 0.8s; }
        .snowflake:nth-child(12) { left: 60%; animation-duration: 5s; animation-delay: 1.3s; }
        .snowflake:nth-child(13) { left: 65%; animation-duration: 6.5s; animation-delay: 1.8s; }
        .snowflake:nth-child(14) { left: 70%; animation-duration: 7.5s; animation-delay: 0.4s; }
        .snowflake:nth-child(15) { left: 75%; animation-duration: 5.5s; animation-delay: 0.9s; }
        .snowflake:nth-child(16) { left: 80%; animation-duration: 6s; animation-delay: 1.4s; }
        .snowflake:nth-child(17) { left: 85%; animation-duration: 7s; animation-delay: 1.9s; }
        .snowflake:nth-child(18) { left: 90%; animation-duration: 5s; animation-delay: 0.6s; }
        .snowflake:nth-child(19) { left: 95%; animation-duration: 6.5s; animation-delay: 1.1s; }
        .snowflake:nth-child(20) { left: 98%; animation-duration: 7.5s; animation-delay: 0.1s; }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans">
    <!-- Hero Section -->
    <section class="bg-cover bg-center bg-no-repeat text-white py-24 relative overflow-hidden" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="container mx-auto px-4 text-center bg-black bg-opacity-50 py-16 relative z-10">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 animate-fadeInUp">Selamat datang di
CV. SEKAR TAMA CONTRACTION</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-4xl mx-auto opacity-90 animate-fadeInUp animation-delay-200">Mitra terpercaya Anda dalam menghadirkan produk dan solusi UPVC berkualitas tinggi. Dengan pengalaman lebih dari 6 tahun, kami melayani pembuatan, pemasangan, hingga desain produk UPVC yang dirancang khusus sesuai kebutuhan Anda.</p>
            <a href="#products" class="inline-block bg-stone-500 text-white hover:text-gray-800 px-8 py-4 rounded-full font-semibold hover:bg-stone-300 transition duration-300 transform hover:scale-105 animate-fadeInUp animation-delay-400">Lihat Produk</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-[#e9e3dc]">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800 animate-fadeInUp">Mengapa Memilih Produk Kami?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-6 bg-stone-500 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 animate-fadeInUp animation-delay-200">
                    <div class="bg-[#e9e3dc] w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Free Pengiriman Jabodetabek</h3>
                    <p class="text-stone-200">Layanan pengiriman gratis untuk wilayah Jabodetabek, sehingga Anda dapat menikmati kemudahan tanpa biaya tambahan.</p>
                </div>
                <div class="text-center p-6 bg-stone-500 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 animate-fadeInUp animation-delay-400">
                    <div class="bg-[#e9e3dc] w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Tahan Lama</h3>
                    <p class="text-stone-200">Tidak mudah lapuk, berkarat, atau keropos seperti material kayu atau logam konvensional.</p>
                </div>
                <div class="text-center p-6 bg-stone-500 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 animate-fadeInUp animation-delay-600">
                    <div class="bg-[#e9e3dc] w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Instalasi Profesional</h3>
                    <p class="text-stone-200">Dapatkan layanan instalasi yang dilakukan oleh tenaga ahli berpengalaman untuk memastikan hasil yang rapi, aman, dan sesuai standar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Preview -->
    <section id="products" class="py-20 bg-[#e9e3dc]">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800 animate-fadeInUp">Produk Unggulan Kami</h2>
            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <?php
                $sql = "SELECT * FROM products LIMIT 2";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '
                        <div class="bg-stone-500 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition-transform duration-300 animate-fadeInUp">
                            <img src="assets/images/'.$row['image'].'" alt="'.$row['name'].'" class="w-full h-48 object-cover">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold mb-2 text-stone-200">'.$row['name'].'</h3>
                                <p class="text-gray-300 mb-4">'.$row['description'].'</p>
                                <div class="flex flex-col mb-4">
                                    <span class="text-stone-200 font-bold text-lg">Rp '.number_format($row['price'], 0, ',', '.').'</span>
                                    '.(!empty($row['size']) ? '<span class="text-stone-300 text-sm">Ukuran: '.htmlspecialchars($row['size']).'</span>' : '').'
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="px-2 py-1 text-xs rounded-full '.($row['type'] == 'pintu' ? 'bg-indigo-900 text-indigo-300' : 'bg-green-900 text-green-300').'">
                                        '.ucfirst($row['type']).'
                                    </span>
                                    <a href="products.php#product-'.$row['id'].'" class="bg-[#e9e3dc] text-gray-800 px-4 py-2 rounded-full hover:bg-stone-300 transition duration-300">Detail</a>
                                </div>
                            </div>
                        </div>';
                    }
                }
                ?>
            </div>
            <div class="text-center mt-8">
                <a href="products.php" class="inline-block bg-stone-500 text-white px-6 py-3 rounded-full font-semibold hover:bg-stone-400 transition duration-300 transform hover:scale-105 animate-pulse-slow">Lihat Semua Produk</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 bg-[#e9e3dc]">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-16 text-gray-800 animate-fadeInUp">Apa Kata Pelanggan Kami?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <?php
                $sql = "SELECT full_name, rating, comment FROM testimonials WHERE status = 'approved' ORDER BY created_at DESC LIMIT 3";
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
                                    <h4 class="font-semibold text-stone-300">' . htmlspecialchars($row['full_name']) . '</h4>
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
                            <p class="text-stone-300">' . htmlspecialchars($row['comment']) . '</p>
                        </div>';
                        $delay += 200;
                    }
                } else {
                    echo '<p class="text-center text-gray-300 col-span-3">Belum ada testimonial yang tersedia.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-cover bg-center bg-no-repeat text-white relative overflow-hidden" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80')">
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="snowflake"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold mb-6 animate-fadeInUp">Siap Memperbarui Rumah Anda?</h2>
            <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto opacity-90 animate-fadeInUp animation-delay-200">Hubungi kami sekarang untuk konsultasi gratis atau kunjungi showroom kami.</p>
            <div class="flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="products.php" class="bg-stone-500 text-white px-8 py-4 rounded-full font-semibold hover:bg-stone-300 hover:text-gray-800 transition duration-300 transform hover:scale-105 animate-fadeInUp animation-delay-400">Lihat Produk</a>
                <a href="#" class="border-2 border-white text-white px-8 py-4 rounded-full font-semibold hover:bg-white hover:text-gray-800 transition duration-300 transform hover:scale-105 animate-fadeInUp animation-delay-600">Hubungi Kami</a>
            </div>
        </div>
    </section>

<?php
require_once 'includes/footer.php';
?>
</body>
</html>