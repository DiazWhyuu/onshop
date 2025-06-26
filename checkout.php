<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Redirect jika belum login atau cart kosong
if (!isLoggedIn()) {
    header("Location: user/login.php?redirect=checkout");
    exit();
}

$cart_items = getCartItems();
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    
    // Validasi
    if (empty($shipping_address)) {
        $error = "Alamat pengiriman harus diisi";
    } else {
        // Buat order
        $order_id = createOrder($_SESSION['user_id'], $shipping_address, $payment_method);
        
        if ($order_id) {
            header("Location: order-confirmation.php?id=" . $order_id);
            exit();
        } else {
            $error = "Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.";
        }
    }
}

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT full_name, email, phone, address FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
ob_end_flush();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>
    
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Shipping and Payment -->
        <div class="lg:w-2/3">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Informasi Pengiriman</h2>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="full_name" class="block text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-4 py-2 border rounded-lg bg-gray-100" readonly>
                    </div>
                    
                    
                    
                    <div class="mb-6">
                        <label for="shipping_address" class="block text-gray-700 mb-2">Alamat Pengiriman</label>
                        <textarea id="shipping_address" name="shipping_address" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required><?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : htmlspecialchars($user['address']); ?></textarea>
                    </div>
                    
                    <h2 class="text-xl font-semibold mb-4">Metode Pembayaran</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center">
                            <input type="radio" id="bank_transfer" name="payment_method" value="bank_transfer" class="h-4 w-4 text-blue-800 focus:ring-blue-500" checked>
                            <label for="bank_transfer" class="ml-3 block text-gray-700">
                                <span class="font-medium">Transfer Bank</span>
                                <p class="text-sm text-gray-500">Transfer ke rekening BCA, Mandiri, atau BRI</p>
                            </label>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="radio" id="cod" name="payment_method" value="cod" class="h-4 w-4 text-blue-800 focus:ring-blue-500">
                            <label for="cod" class="ml-3 block text-gray-700">
                                <span class="font-medium">Cash on Delivery (COD)</span>
                                <p class="text-sm text-gray-500">Bayar saat produk diterima</p>
                            </label>
                        </div>
                    </div>

                    <!-- Bukti Transfer Section -->
                    <div id="bank-transfer-proof" class="mb-6">
                        <h3 class="text-lg font-medium mb-2">Upload Bukti Transfer</h3>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mt-1 text-sm text-gray-600">Upload bukti transfer bank Anda</p>
                                <input type="file" id="payment_proof" name="payment_proof" accept="image/*,.pdf" class="mt-4 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, atau PDF (maks. 2MB)</p>
                            </div>
                            <div id="payment-proof-preview" class="mt-4 text-center hidden">
                                <img id="proof-preview-image" src="#" alt="Preview Bukti Transfer" class="max-h-40 mx-auto mb-2 rounded">
                                <p class="text-sm text-green-600">Preview bukti transfer</p>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-800 text-white py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-300">
                        <i class="fas fa-credit-card mr-2"></i> Buat Pesanan
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Ringkasan Pesanan</h2>
                
                <div class="divide-y divide-gray-200">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="py-4 flex justify-between">
                            <div class="flex items-center">
                                <img src="assets/images/<?php echo $item['product']['image']; ?>" alt="<?php echo $item['product']['name']; ?>" class="h-12 w-12 object-cover rounded">
                                <div class="ml-3">
                                    <h3 class="text-gray-800"><?php echo $item['product']['name']; ?></h3>
                                    <p class="text-gray-500 text-sm"><?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['product']['price'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                            <span class="font-medium">Rp <?php echo number_format($item['product']['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="border-t border-gray-200 my-4"></div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span>Rp <?php echo number_format(getCartTotal(), 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pengiriman</span>
                        <span>Gratis</span>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 my-4"></div>
                
                <div class="flex justify-between text-lg font-semibold">
                    <span>Total</span>
                    <span>Rp <?php echo number_format(getCartTotal(), 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>

        <!-- No Rekening -->
        <div id="rekening-info" class="lg:w-1/3">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Nomor Rekening</h2>
                
                <div class="flex justify-between w-full">
                    <img class="h-5" src="https://logos-download.com/wp-content/uploads/2017/03/BCA_logo_Bank_Central_Asia.png" alt="">
                    <p>23452345</p>
                </div>
                <div class="border-t border-gray-200 my-4"></div>

                <div class="flex justify-between w-full">
                    <img class="h-5" src="https://jasalogocepat.com/wp-content/uploads/2023/12/Logo-Bank-Mandiri-PNG-1024x298.png" alt="">
                    <p>23452345</p>
                </div>
                <div class="border-t border-gray-200 my-4"></div>

                <div class="flex justify-between w-full">
                    <img class="h-7" src="https://www.freelogovectors.net/wp-content/uploads/2023/02/bri-logo-freelogovectors.net_.png" alt="">
                    <p>23452345</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const bankTransferProof = document.getElementById('bank-transfer-proof');
    const paymentProofInput = document.getElementById('payment_proof');
    const proofPreview = document.getElementById('payment-proof-preview');
    const proofImage = document.getElementById('proof-preview-image');

    // Tampilkan/sembunyikan form upload bukti transfer
    function toggleBankTransferProof() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        if (selectedMethod === 'bank_transfer') {
            bankTransferProof.classList.remove('hidden');
        } else {
            bankTransferProof.classList.add('hidden');
            paymentProofInput.value = '';
            proofPreview.classList.add('hidden');
        }
    }

    // Inisialisasi saat halaman dimuat
    toggleBankTransferProof();

    // Tambahkan event listener untuk perubahan metode pembayaran
    paymentMethods.forEach(method => {
        method.addEventListener('change', toggleBankTransferProof);
    });

    // Preview gambar sebelum upload
    if (paymentProofInput) {
        paymentProofInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    proofImage.src = e.target.result;
                    proofPreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                proofPreview.classList.add('hidden');
            }
        });
    }

    // Ambil elemen radio dan info rekening
    const bankTransferRadio = document.getElementById('bank_transfer');
    const codRadio = document.getElementById('cod');
    const rekeningInfo = document.getElementById('rekening-info');

    // Fungsi untuk toggle tampilan rekening
    function toggleRekeningInfo() {
        if (bankTransferRadio.checked) {
            rekeningInfo.classList.remove('hidden');
        } else {
            rekeningInfo.classList.add('hidden');
        }
    }

    // Event listeners
    bankTransferRadio.addEventListener('change', toggleRekeningInfo);
    codRadio.addEventListener('change', toggleRekeningInfo);

    // Jalankan saat load pertama kali
    window.addEventListener('DOMContentLoaded', toggleRekeningInfo);
});
</script>

<?php
require_once 'includes/footer.php';
?>