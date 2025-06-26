<?php
ob_start(); // Start output buffering
require_once 'includes/header.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Get current settings from database
$current_settings = [];
$sql = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('site_title', 'site_description', 'admin_email', 'phone_number', 'address', 'site_logo')";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

// Set default values if not found in database
$defaults = [
    'site_title' => 'UPVC Store',
    'site_description' => 'Toko Online Pintu dan Jendela UPVC',
    'admin_email' => 'admin@upvcstore.com',
    'phone_number' => '(021) 1234-5678',
    'address' => 'Jl. UPVC Modern No. 123, Jakarta Selatan, 12345, Indonesia',
    'site_logo' => 'logo.png'
];
$current_settings = array_merge($defaults, $current_settings);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $message = trim($_POST['message']);

    // Validation
    $errors = [];
    if (empty($name))
        $errors['name'] = "Nama harus diisi";
    if (empty($message))
        $errors['message'] = "Pesan harus diisi";

    if (empty($errors)) {
        // Format phone number for WhatsApp (remove non-digit characters)
        $phone_number = preg_replace('/[^0-9]/', '', $current_settings['phone_number']);
        if (substr($phone_number, 0, 1) === '0') {
            $phone_number = '+62' . substr($phone_number, 1);
        }

        // Prepare WhatsApp message
        $whatsapp_message = urlencode("Nama: $name\nPesan: $message");
        $whatsapp_url = "https://wa.me/$phone_number?text=$whatsapp_message";

        // Redirect to WhatsApp
        ob_end_clean(); // Clear output buffer before redirect
        header("Location: $whatsapp_url");
        exit();
    }
}
?>

<div class="bg-[#e9e3dc] py-16 pb-64 text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800 text-center mb-12 animate-slideInDown">Hubungi
                Kami</h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <!-- Contact Form -->
                <div class="bg-stone-500 rounded-2xl shadow-xl p-10 border border-gray-700 animate-fadeInUp">
                    <h2 class="text-3xl font-bold text-white mb-6">Kirim Pesan</h2>
                    <?php if (isset($errors['general'])): ?>
                        <div class="bg-red-900 border border-red-700 text-red-300 px-6 py-4 rounded-lg mb-8 animate-pulse">
                            <?php echo htmlspecialchars($errors['general']); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-6">
                            <label for="name" class="block text-white font-semibold mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name"
                                value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                                class="w-full px-4 py-3 bg-stone-300 border border-gray-600 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all duration-300 <?php echo isset($errors['name']) ? 'border-red-500' : ''; ?>"
                                required>
                            <?php if (isset($errors['name'])): ?>
                                <p class="text-red-400 text-sm mt-1"><?php echo htmlspecialchars($errors['name']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="mb-8">
                            <label for="messageinspection" class="block text-white-200 font-semibold mb-2">Pesan</label>
                            <textarea id="message" name="message" rows="5"
                                class="w-full px-4 py-3 bg-stone-300 border border-gray-600 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition-all duration-300 <?php echo isset($errors['message']) ? 'border-red-500' : ''; ?>"
                                required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                            <?php if (isset($errors['message'])): ?>
                                <p class="text-red-400 text-sm mt-1"><?php echo htmlspecialchars($errors['message']); ?></p>
                            <?php endif; ?>
                        </div>
                        <button type="submit"
                            class="w-full bg-[#e9e3dc] text-gray-800 py-3 px-6 rounded-full font-semibold hover:bg-stone-300 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-opacity-50 transition-all duration-300 transform hover:scale-105">
                            Kirim Pesan
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div
                    class="bg-stone-500 backdrop-blur-md rounded-2xl shadow-xl p-10 border border-gray-700 animate-fadeInUp animation-delay-200">
                    <h2 class="text-3xl font-bold text-white mb-6">Informasi Kontak</h2>
                    <div class="space-y-8">
                        <div class="flex items-start transform transition-all duration-300 hover:-translate-y-1">
                            <div class="bg-[#e9e3dc] p-4 rounded-full mr-5">
                                <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">Alamat</h3>
                                <p class="text-white">
                                    <?php echo nl2br(htmlspecialchars($current_settings['address'])); ?></p>
                            </div>
                        </div>
                        <div class="flex items-start transform transition-all duration-300 hover:-translate-y-1">
                            <div class="bg-[#e9e3dc] p-4 rounded-full mr-5">
                                <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">Telepon</h3>
                                <p class="text-white"><a
                                        href="tel:<?php echo htmlspecialchars($current_settings['phone_number']); ?>"
                                        class="hover:text-gray-800"><?php echo htmlspecialchars($current_settings['phone_number']); ?></a>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start transform transition-all duration-300 hover:-translate-y-1">
                            <div class="bg-[#e9e3dc] p-4 rounded-full mr-5">
                                <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">Email</h3>
                                <p class="text-white"><a
                                        href="mailto:<?php echo htmlspecialchars($current_settings['admin_email']); ?>"
                                        class="hover:text-gray-800"><?php echo htmlspecialchars($current_settings['admin_email']); ?></a>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start transform transition-all duration-300 hover:-translate-y-1">
                            <div class="bg-[#e9e3dc] p-4 rounded-full mr-5">
                                <svg class="w-8 h-8 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-white">Jam Operasional</h3>
                                <p class="text-white">Senin - Jumat: 08:00 - 17:00<br>Sabtu: 09:00 - 15:00<br>Minggu &
                                    Hari Libur: Tutup</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map -->
            <div
                class="mt-12 bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-700 transform transition-all duration-300 hover:scale-[1.02] animate-fadeInUp animation-delay-400">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3965.5919117905796!2d106.69663617499117!3d-6.317213993672178!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMTknMDIuMCJTIDEwNsKwNDEnNTcuMiJF!5e0!3m2!1sen!2sid!4v1748695523225!5m2!1sen!2sid"
                    width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
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

    .animation-delay-200 {
        animation-delay: 0.2s;
    }

    .animation-delay-400 {
        animation-delay: 0.4s;
    }
</style>

<?php
ob_end_flush(); // Flush output buffer at the end
require_once 'includes/footer.php';
?>