<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/functions.php';

// Handle cart actions
if (isset($_GET['action'])) {
    if (!isLoggedIn()) {
        header("Location: user/login.php");
        exit();
    }
    
    switch ($_GET['action']) {
        case 'add':
            if (isset($_POST['product_id'])) {
                $product_id = (int)$_POST['product_id'];
                addToCart($product_id);
                $_SESSION['success_message'] = "Produk berhasil ditambahkan ke keranjang";
                header("Location: products.php");
                exit();
            }
            break;
            
        case 'update':
            if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                $product_id = (int)$_POST['product_id'];
                $quantity = (int)$_POST['quantity'];
                updateCart($product_id, $quantity);
                
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    // Jika request AJAX, kembalikan response JSON
                    $cart_items = getCartItems();
                    $item = $cart_items[$product_id];
                    $response = [
                        'success' => true,
                        'subtotal' => number_format($item['product']['price'] * $item['quantity'], 0, ',', '.'),
                        'total' => number_format(getCartTotal(), 0, ',', '.')
                    ];
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit();
                } else {
                    $_SESSION['success_message'] = "Keranjang berhasil diperbarui";
                    header("Location: cart.php");
                    exit();
                }
            }
            break;
            
        case 'remove':
            if (isset($_GET['id'])) {
                removeFromCart((int)$_GET['id']);
                $_SESSION['success_message'] = "Produk berhasil dihapus dari keranjang";
                header("Location: cart.php");
                exit();
            }
            break;
            
        case 'clear':
            unset($_SESSION['cart']);
            $_SESSION['success_message'] = "Keranjang berhasil dikosongkan";
            header("Location: cart.php");
            exit();
            break;
    }
}

$cart_items = getCartItems();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{
            background-color: #e9e3dc;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out;
        }
        .quantity-input {
            transition: all 0.3s ease;
        }
        .quantity-input:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 2px rgba(129, 140, 248, 0.5);
        }
    </style>
</head>
<body class="bg-[#e9e3dc] text-gray-100 min-h-screen">
    <div class="container bg-[#e9e3dc] mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold mb-8 text-gray-800 animate-fadeIn">Keranjang Belanja</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-800 border border-green-600 text-green-100 px-4 py-3 rounded-lg mb-6 animate-fadeIn">
                <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($cart_items)): ?>
            <div class="bg-stone-500 rounded-lg shadow-xl overflow-hidden mb-6">
                <table class="min-w-full">
                    <thead class="bg-stone-400">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 uppercase tracking-wider">Produk</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-800 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        <?php foreach ($cart_items as $item): ?>
                            <tr class="text-white hover:text-gray-800 bg-stone-500 hover:bg-stone-400 transition-all animate-fadeIn" data-product-id="<?php echo $item['product']['id']; ?>">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="assets/images/<?php echo $item['product']['image']; ?>" alt="<?php echo $item['product']['name']; ?>" 
                                             class="h-16 w-16 object-cover rounded-lg">
                                        <div class="ml-4">
                                            <h3 class="text-lg font-medium"><?php echo $item['product']['name']; ?></h3>
                                            <p class="text-gray-800 text-sm"><?php echo ucfirst($item['product']['type']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 product-price" data-price="<?php echo $item['product']['price']; ?>">
                                    Rp <?php echo number_format($item['product']['price'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="<?php echo $item['product']['stock']; ?>"
                                           class="quantity-input w-20 px-2 py-1 border border-gray-600 rounded-lg bg-stone-400 text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </td>
                                <td class="px-6 py-4 font-medium product-subtotal">
                                    Rp <?php echo number_format($item['product']['price'] * $item['quantity'], 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="cart.php?action=remove&id=<?php echo $item['product']['id']; ?>" 
                                       class="text-red-500 hover:text-red-300 transition-all">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="flex justify-end items-center mb-8">
                <a href="cart.php?action=clear" 
                   class="text-red-500 hover:text-red-300 transition-all">
                    <i class="fas fa-trash mr-2"></i> Kosongkan Keranjang
                </a>
            </div>
            
            <div class="bg-stone-500 rounded-lg shadow-xl p-6 max-w-md ml-auto transform transition-all hover:shadow-2xl">
                <h2 class="text-xl font-semibold mb-4 text-white">Ringkasan Belanja</h2>
                
                <div class="flex justify-between mb-2">
                    <span class="text-white">Subtotal</span>
                    <span class="cart-subtotal text-white">Rp <?php echo number_format(getCartTotal(), 0, ',', '.'); ?></span>
                </div>
                
                <div class="flex justify-between mb-2">
                    <span class="text-white">Pengiriman</span>
                    <span class="text-white">Gratis</span>
                </div>
                
                <div class="border-t border-gray-600 my-4"></div>
                
                <div class="flex justify-between text-lg font-semibold mb-6">
                    <span class="text-white">Total</span>
                    <span class="text-white cart-total">Rp <?php echo number_format(getCartTotal(), 0, ',', '.'); ?></span>
                </div>
                
                <a href="checkout.php" 
                   class="block w-full bg-green-600 text-white text-center py-3 px-4 rounded-lg hover:bg-green-700 transition-all transform hover:scale-105">
                    <i class="fas fa-credit-card mr-2"></i> Lanjut ke Pembayaran
                </a>
            </div>
        <?php else: ?>
            <div class="bg-stone-500 rounded-lg shadow-xl p-8 text-center transform transition-all hover:shadow-2xl animate-fadeIn">
                <i class="fas fa-shopping-cart text-5xl text-white mb-4"></i>
                <h2 class="text-xl font-semibold mb-2 text-white">Keranjang Anda Kosong</h2>
                <p class="text-white mb-4">Tambahkan produk ke keranjang untuk melanjutkan</p>
                <a href="products.php" 
                   class="inline-block bg-[#e9e3dc] text-gray-800 px-6 py-2 rounded-lg hover:bg-stone-300 transition-all transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali ke Produk
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format angka ke format Rupiah
        const formatRupiah = (number) => {
            return 'Rp ' + number.toLocaleString('id-ID');
        };

        // Fungsi untuk update cart via AJAX
        const updateCart = async (productId, quantity) => {
            try {
                const response = await fetch('cart.php?action=update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                });

                const data = await response.json();

                if (data.success) {
                    // Update ringkasan belanja dari response server
                    document.querySelector('.cart-subtotal').textContent = formatRupiah(parseInt(data.total.replace(/[^\d]/g, '')));
                    document.querySelector('.cart-total').textContent = formatRupiah(parseInt(data.total.replace(/[^\d]/g, '')));
                }
            } catch (error) {
                console.error('Error updating cart:', error);
            }
        };

        // Fungsi debounce untuk membatasi frekuensi pemanggilan fungsi
        const debounce = (func, delay) => {
            let timeoutId;
            return function(...args) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                }, delay);
            };
        };

        // Ambil semua input quantity
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        // Tambahkan event listener untuk setiap input
        quantityInputs.forEach(input => {
            input.addEventListener('input', debounce(function() {
                const row = this.closest('tr');
                const productId = row.dataset.productId;
                const quantity = parseInt(this.value);
                const price = parseFloat(row.querySelector('.product-price').dataset.price);
                const subtotalElement = row.querySelector('.product-subtotal');
                
                // Validasi input
                const max = parseInt(this.max);
                const min = parseInt(this.min);
                
                if (quantity < min || quantity > max) {
                    this.classList.add('border-red-500');
                    return;
                }
                
                this.classList.remove('border-red-500');
                
                // Hitung subtotal
                const subtotal = price * quantity;
                
                // Update subtotal
                subtotalElement.textContent = formatRupiah(subtotal);
                
                // Update ringkasan belanja
                updateCartSummary();
                
                // Kirim update ke server
                updateCart(productId, quantity);
            }, 500)); // Debounce 500ms
        });
        
        // Fungsi untuk update ringkasan belanja
        function updateCartSummary() {
            let subtotal = 0;
            
            // Hitung total dari semua subtotal
            document.querySelectorAll('.product-subtotal').forEach(element => {
                const value = element.textContent.replace(/[^\d]/g, '');
                subtotal += parseInt(value);
            });
            
            // Update ringkasan belanja
            const cartSubtotal = document.querySelector('.cart-subtotal');
            const cartTotal = document.querySelector('.cart-total');
            
            if (cartSubtotal && cartTotal) {
                cartSubtotal.textContent = formatRupiah(subtotal);
                cartTotal.textContent = formatRupiah(subtotal);
            }
        }
    });
    </script>
</body>
</html>