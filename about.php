<?php
require_once 'includes/header.php';
?>

<div class="bg-[#e9e3dc] py-16 pb-64 text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800 text-center mb-12 animate-slideInDown">Tentang UPVC Store</h1>

            <!-- About Us Section -->
            <div class="bg-stone-500 rounded-2xl shadow-xl p-10 mb-10 border border-gray-700 animate-fadeInUp">
                <h2 class="text-3xl font-bold text-white mb-6">Siapa Kami?</h2>
                <p class="text-white leading-relaxed mb-8">
                    CV. SEKAR TAMA adalah mitra terpercaya Anda dalam menghadirkan solusi pintu dan jendela UPVC berkualitas tinggi sejak tahun 2019. Dengan pengalaman lebih dari 6 tahun, kami berkomitmen untuk memberikan layanan terbaik yang berfokus pada kebutuhan UPVC.
                </p>
                <div class="space-y-8">
                    <div class="flex items-center transform transition-all duration-300 hover:-translate-y-1 animate-fadeInUp animation-delay-200">
                        <div class="bg-[#e9e3dc] p-4 rounded-full mr-5">
                            <svg class="w-10 h-10 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Tim Professional</h3>
                            <p class="text-white">Kami memiliki tim tenaga ahli yang berpengalaman dan profesional untuk memastikan hasil yang berkualitas.</p>
                        </div>
                    </div>
                    <div class="flex items-center transform transition-all duration-300 hover:-translate-y-1 animate-fadeInUp animation-delay-400">
                        <div class="bg-[#e9e3dc] p-4 rounded-full mr-5">
                            <svg class="w-10 h-10 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Pengalaman dan Sejarah</h3>
                            <p class="text-white">Lebih dari 6 tahun menghadirkan layanan unggulan dengan hasil memuaskan.</p>
                        </div>
                    </div>
                    <div class="flex items-center transform transition-all duration-300 hover:-translate-y-1 animate-fadeInUp animation-delay-600">
                        <div class="bg-[#e9e3dc] p-4 rounded-full mr-5">
                            <svg class="w-10 h-10 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white">Hasil Ekonomi yang Optimal</h3>
                            <p class="text-white">Kami membantu klien mencapai efisiensi biaya tanpa mengurangi kualitas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vision & Mission Section -->
            <div class="bg-stone-500 rounded-2xl shadow-xl p-10 mb-10 border border-gray-700 animate-fadeInUp animation-delay-200">
                <h2 class="text-3xl font-bold text-white mb-6">Visi & Misi</h2>
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-white mb-3">Visi</h3>
                    <p class="text-white leading-relaxed">
                        Menjadi penyedia solusi pintu dan jendela UPVC terdepan di Indonesia dengan inovasi terus-menerus 
                        untuk meningkatkan kenyamanan hidup masyarakat.
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-white mb-3">Misi</h3>
                    <ul class="list-disc list-inside text-white space-y-3">
                        <li class="transform transition-all duration-300 hover:translate-x-2">Menyediakan produk berkualitas tinggi dengan harga kompetitif</li>
                        <li class="transform transition-all duration-300 hover:translate-x-2">Memberikan pelayanan terbaik kepada pelanggan</li>
                        <li class="transform transition-all duration-300 hover:translate-x-2">Terus berinovasi dalam desain dan teknologi produk</li>
                        <li class="transform transition-all duration-300 hover:translate-x-2">Mendukung pembangunan berkelanjutan dengan produk ramah lingkungan</li>
                        <li class="transform transition-all duration-300 hover:translate-x-2">Mengedukasi masyarakat tentang manfaat penggunaan UPVC</li>
                    </ul>
                </div>
            </div>

            <!-- Team Section -->
           
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
</style>

<?php
require_once 'includes/footer.php';
?>