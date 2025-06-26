<?php
require_once 'config.php';

/**
 * Mendapatkan semua produk
 */

function getAllProducts($type = null)
{
    global $conn;

    $sql = "SELECT * FROM products";
    if ($type) {
        $sql .= " WHERE type = ?";
    }
    $sql .= " ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    if ($type) {
        $stmt->bind_param("s", $type);
    }
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Mendapatkan produk berdasarkan ID
 */
function getProductById($id)
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Menambahkan produk ke cart
 */
function addToCart($product_id, $quantity = 1)
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

/**
 * Mengupdate quantity cart
 */
function updateCart($product_id, $quantity)
{
    if (isset($_SESSION['cart'][$product_id])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
}

/**
 * Menghapus item dari cart
 */
function removeFromCart($product_id)
{
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

/**
 * Mendapatkan isi cart dengan detail produk
 */
function getCartItems()
{
    if (empty($_SESSION['cart'])) {
        return [];
    }

    global $conn;
    $cart_items = [];
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));

    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
    $stmt->execute();
    $products = $stmt->get_result();

    while ($product = $products->fetch_assoc()) {
        $cart_items[] = [
            'product' => $product,
            'quantity' => $_SESSION['cart'][$product['id']]
        ];
    }

    return $cart_items;
}

/**
 * Menghitung total cart
 */
function getCartTotal()
{
    $total = 0;
    $cart_items = getCartItems();

    foreach ($cart_items as $item) {
        $total += $item['product']['price'] * $item['quantity'];
    }

    return $total;
}

/**
 * Menyimpan order ke database
 */
/**
 * Menyimpan order ke database
 */
function createOrder($user_id, $shipping_address, $payment_method)
{
    global $conn;

    $conn->begin_transaction();

    try {
        // Handle file upload for bank transfer
        $payment_proof = null;
        if ($payment_method === 'bank_transfer' && isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $file = $_FILES['payment_proof'];

            if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
                $upload_dir = 'uploads/payment_proofs/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $file_name = uniqid() . '-' . basename($file['name']);
                $file_path = $upload_dir . $file_name;

                if (move_uploaded_file($file['tmp_name'], $file_path)) {
                    $payment_proof = $file_path;
                } else {
                    throw new Exception("Gagal mengunggah file bukti transfer.");
                }
            } else {
                throw new Exception("File tidak valid atau melebihi ukuran maksimum 2MB.");
            }
        }

        // Simpan order utama
        $total = getCartTotal();
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, payment_proof) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $user_id, $total, $shipping_address, $payment_method, $payment_proof);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Simpan order items
        $cart_items = getCartItems();
        foreach ($cart_items as $item) {
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $price = $item['product']['price'];
            $stmt->bind_param("iiid", $order_id, $item['product']['id'], $item['quantity'], $price);
            $stmt->execute();

            // Update stock
            $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->bind_param("ii", $item['quantity'], $item['product']['id']);
            $stmt->execute();
        }

        // Kosongkan cart
        unset($_SESSION['cart']);

        $conn->commit();
        return $order_id;
    } catch (Exception $e) {
        $conn->rollback();
        error_log($e->getMessage());
        return false;
    }
}

/**
 * Mendapatkan order history user
 */
function getUserOrders($user_id)
{
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Mendapatkan detail order
 */
function getOrderDetails($order_id)
{
    global $conn;

    // Get order info
    $stmt = $conn->prepare("SELECT o.*, u.full_name, u.email, u.phone FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        return false;
    }

    // Get order items
    $stmt = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items = $stmt->get_result();

    $order['items'] = [];
    while ($item = $items->fetch_assoc()) {
        $order['items'][] = $item;
    }

    return $order;
}
