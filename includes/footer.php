<?php
?>
    <!-- Footer -->
    <footer class="bg-[#e9e3dc] text-white py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <!-- Brand Section -->
                <div>
                    <h3 class="text-2xl font-extrabold mb-4 text-gray-800">UPVC Store</h3>
                    <p class="text-gray-800 leading-relaxed">Spesialis pintu dan jendela UPVC berkualitas tinggi untuk rumah modern Anda.</p>
                    
                </div>

                <!-- Products Section -->
                <div>
                    <h4 class="text-xl font-bold mb-4 text-gray-800">Produk</h4>
                    <ul class="space-y-3">
                        <li><a href="products.php?type=pintu" class="text-gray-700 hover:text-gray-800 transition-colors">Pintu UPVC</a></li>
                        <li><a href="products.php?type=jendela" class="text-gray-700 hover:text-gray-800 transition-colors">Jendela UPVC</a></li>
                        <li><a href="products.php" class="text-gray-700 hover:text-gray-800 transition-colors">Semua Produk</a></li>
                    </ul>
                </div>

                <!-- Links Section -->
                <div>
                    <h4 class="text-xl font-bold mb-4 text-gray-800">Tautan</h4>
                    <ul class="space-y-3">
                        <li><a href="about.php" class="text-gray-700 hover:text-gray-800 transition-colors">Tentang Kami</a></li>
                        <li><a href="contact.php" class="text-gray-700 hover:text-gray-800 transition-colors">Kontak</a></li>
                        <li><a href="#" class="text-gray-700 hover:text-gray-800 transition-colors">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="text-gray-700 hover:text-gray-800 transition-colors">Kebijakan Privasi</a></li>
                    </ul>
                </div>

                <!-- Contact Section -->
                <div>
                    <h4 class="text-xl font-bold mb-4 text-gray-800">Hubungi Kami</h4>
                    <address class="text-gray-800 not-italic space-y-4">
                        <p class="flex items-start">
                            <svg class="w-6 h-6 mr-3 text-gray-800 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Jl. Maruga ciater, Gg.Waru, RT 04/09, no.67, Serpong, Tanggerang Selatan, Banten, ID, 15317
                        </p>
                        <p class="flex items-center">
                            <svg class="w-6 h-6 mr-3 text-gray-800 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <a href="tel:+6285156065079" class="hover:text-gray-800 transition-colors">+62-851-5606-5079</a>
                        </p>
                        <p class="flex items-center">
                            <svg class="w-6 h-6 mr-3 text-gray-800 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <a href="admin@sekartama-upvc.com" class="hover:text-gray-800 transition-colors">admin@sekartama-upvc.com</a>
                        </p>
                    </address>
                </div>
            </div>

            <!-- Divider and Copyright -->
            <div class="mt-12 pt-8 text-center">
                <div class="h-px bg-gradient-to-r from-transparent via-indigo-600 to-transparent mb-6"></div>
                <p class="text-gray-300">© <?php echo date('Y'); ?> UPVC Store. All rights reserved.</p>
            </div>
        </div>

        <!-- ... konten footer lainnya ... -->

<!-- Script untuk toggle bukti transfer -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bankTransferRadio = document.getElementById('bank_transfer');
    const codRadio = document.getElementById('cod');
    const proofSection = document.getElementById('bank-transfer-proof');
    
    if (bankTransferRadio && codRadio && proofSection) {
        function toggleProofSection() {
            if (bankTransferRadio.checked) {
                proofSection.classList.remove('hidden');
                document.getElementById('payment_proof').setAttribute('required', 'required');
            } else {
                proofSection.classList.add('hidden');
                document.getElementById('payment_proof').removeAttribute('required');
            }
        }
        
        bankTransferRadio.addEventListener('change', toggleProofSection);
        codRadio.addEventListener('change', toggleProofSection);
        toggleProofSection();
    }
});
</script>

</body>
</html>
    </footer>
</body>
</html>