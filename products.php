<?php
require_once 'includes/header.php';
require_once 'includes/functions.php';

$type = isset($_GET['type']) ? $_GET['type'] : null;
$products = getAllProducts($type);
?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Tangkap semua form tambah ke keranjang
        const forms = document.querySelectorAll("form[action=\"cart.php?action=add\"]");

        forms.forEach(form => {
            form.addEventListener("submit", function (e) {
                e.preventDefault(); // Mencegah submit default

                // Kirim form dengan AJAX
                fetch(form.action, {
                    method: "POST",
                    body: new FormData(form)
                })
                    .then(response => response.text())
                    .then(data => {
                        // Tampilkan notifikasi
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: "Pesanan telah ditambahkan ke keranjang",
                            confirmButtonColor: "#6366f1",
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: "Terjadi kesalahan saat menambahkan ke keranjang",
                            confirmButtonColor: "#6366f1",
                        });
                    });
            });
        });
    });
</script>

<div class="mx-auto px-4 sm:px-6 md:px-10 lg:px-20 xl:px-40 py-8 md:py-12 pb-16 md:pb-64 bg-[#e9e3dc] text-white">
    <!-- Header and Filters -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 md:mb-12">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-800 animate-fadeInUp text-center md:text-left w-full md:w-auto">
            <?php
            if ($type == 'pintu') {
                echo 'Pintu UPVC';
            } elseif ($type == 'jendela') {
                echo 'Jendela UPVC';
            } else {
                echo 'Semua Produk';
            }
            ?>
        </h1>
        <div class="flex flex-wrap justify-center gap-2 sm:space-x-3 mt-4 sm:mt-6 md:mt-0 animate-fadeInUp animation-delay-200">
            <a href="products.php"
                class="px-4 sm:px-5 py-2 sm:py-3 rounded-full font-semibold text-xs sm:text-sm transition-all duration-300 <?php echo !$type ? 'bg-stone-500 text-white shadow-lg' : 'bg-gray-700 text-gray-200 hover:bg-stone-400'; ?>">
                Semua
            </a>
            <a href="products.php?type=pintu"
                class="px-4 sm:px-5 py-2 sm:py-3 rounded-full font-semibold text-xs sm:text-sm transition-all duration-300 <?php echo $type == 'pintu' ? 'bg-stone-500 text-white shadow-lg' : 'bg-gray-700 text-gray-200 hover:bg-stone-400'; ?>">
                Pintu
            </a>
            <a href="products.php?type=jendela"
                class="px-4 sm:px-5 py-2 sm:py-3 rounded-full font-semibold text-xs sm:text-sm transition-all duration-300 <?php echo $type == 'jendela' ? 'bg-stone-500 text-white shadow-lg' : 'bg-gray-700 text-gray-200 hover:bg-stone-400'; ?>">
                Jendela
            </a>
        </div>
    </div>

    <!-- Products Grid -->
    <?php if ($products->num_rows > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 md:gap-8">
            <?php while ($product = $products->fetch_assoc()): ?>
                <div
                    class="bg-stone-500 rounded-xl shadow-xl overflow-hidden border border-gray-700 transform transition-all duration-500 hover:scale-105 hover:shadow-2xl animate-fadeInUp animation-delay-<?php echo (rand(0, 3) * 200); ?>">
                    <div class="relative">
                        <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"
                            class="w-full h-48 sm:h-56 object-cover transition-transform duration-300 hover:scale-110">
                        <?php if ($product['stock'] > 0): ?>
                            <span
                                class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-green-500 text-white text-xs font-semibold px-2 sm:px-3 py-1 rounded-full shadow">Tersedia</span>
                        <?php else: ?>
                            <span
                                class="absolute top-2 sm:top-3 right-2 sm:right-3 bg-red-500 text-white text-xs font-semibold px-2 sm:px-3 py-1 rounded-full shadow">Habis</span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg sm:text-xl font-bold text-white mb-2 sm:mb-3"><?php echo $product['name']; ?></h3>
                        <p class="text-stone-200 mb-3 sm:mb-4 line-clamp-3 text-sm sm:text-base"><?php echo $product['description']; ?></p>
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-stone-200 font-bold text-base sm:text-lg">Rp
                                    <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                <?php if (!empty($product['size'])): ?>
                                    <span class="text-stone-300 text-xs sm:text-sm">Ukuran:
                                        <?php echo htmlspecialchars($product['size']); ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (isLoggedIn() && $product['stock'] > 0): ?>
                                <form method="post" action="cart.php?action=add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit"
                                        class="bg-gray-800 text-white px-3 sm:px-4 py-1 sm:py-2 rounded-full font-semibold text-sm sm:text-base hover:bg-gray-700 transition-all duration-300 transform hover:scale-105">
                                        <i class="fas fa-cart-plus mr-1 sm:mr-2"></i> Tambah
                                    </button>
                                </form>
                            <?php elseif (!isLoggedIn()): ?>
                                <button disabled
                                    class="bg-gray-600 text-gray-400 px-3 sm:px-4 py-1 sm:py-2 rounded-full font-semibold text-sm sm:text-base cursor-not-allowed"
                                    title="Login untuk memesan">
                                    <i class="fas fa-cart-plus mr-1 sm:mr-2"></i> Tambah
                                </button>
                            <?php else: ?>
                                <button disabled
                                    class="bg-gray-600 text-gray-400 px-3 sm:px-4 py-1 sm:py-2 rounded-full font-semibold text-sm sm:text-base cursor-not-allowed">
                                    Stok Habis
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="flex mt-4 sm:mt-5">
                            <a href="https://wa.me/6282124573017" class="w-full py-2 rounded-full bg-gray-800 text-center justify-center hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 text-sm sm:text-base">Custom Ukuran</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="bg-gray-800 rounded-xl shadow-xl p-6 sm:p-10 text-center border border-gray-700 animate-fadeInUp">
            <p class="text-gray-400 text-base sm:text-lg">Tidak ada produk yang tersedia.</p>
        </div>
    <?php endif; ?>
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

    .animate-fadeInUp {
        animation: fadeInUp 0.8s ease-out forwards;
    }

    .animation-delay-200 {
        animation-delay: 0.2s;
    }

    .animation-delay-400 {
        animation-delay: 0.4s;
    }

    .animation-delay-600 {
        animation-delay: 0.6s;
    }
</style>

<?php
require_once 'includes/footer.php';
?>