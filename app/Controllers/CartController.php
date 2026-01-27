<?php

class CartController extends Controller {
    
    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 0;
        $coupon = $_SESSION['applied_coupon'] ?? null;
        if ($coupon) {
            $discount = ($coupon['type'] === 'percent') ? ($subtotal * $coupon['value'] / 100) : $coupon['value'];
        }
        if ($discount > $subtotal) $discount = $subtotal;
        $total = $subtotal - $discount;

        $this->view('user.cart.index', [
            'title' => 'Giỏ hàng - TechMart',
            'cart' => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon' => $coupon,
            'total' => $total
        ]);
    }

    public function add($id, $variantId = null) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $productModel = $this->model('Product');
        $product = $productModel->show($id);

        if (!$product) {
            $this->redirect('product/index');
            return;
        }

        $cartKey = "p_" . $id; 
        $name = $product['name'];
        $price = $product['price'];
        $image = $product['image'];
        $attributes = ""; 

        if ($variantId && $variantId !== 'undefined' && $variantId > 0) {
            $variant = $this->model('Model')->query(
                "SELECT v.*, c.name as color_name, s.name as size_name 
                 FROM product_variants v 
                 LEFT JOIN colors c ON v.color_id = c.id 
                 LEFT JOIN sizes s ON v.size_id = s.id 
                 WHERE v.id = ? AND v.product_id = ?", [$variantId, $id]
            )->fetch();

            if ($variant) {
                $cartKey = "v_" . $variantId; 
                $price = $variant['price'];
                $image = (!empty($variant['image']) && $variant['image'] != 'default.jpg') ? $variant['image'] : $product['image'];
                $attributes = ($variant['color_name'] ?: "") . ($variant['size_name'] ? " - " . $variant['size_name'] : "");
            }
        }
        if (!isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey] = [
                'id'         => $id,
                'variant_id' => $variantId,
                'name'       => $name,
                'attributes' => $attributes,
                'price'      => (float)$price,
                'image'      => $image,
                'quantity'   => 1
            ];
        } else {
            $_SESSION['cart'][$cartKey]['quantity']++;
        }

        $_SESSION['success'] = "Đã thêm vào giỏ hàng!";
        session_write_close();
        
        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/product/index');
        header("Location: " . $referer);
        exit();
    }

    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $key = $_POST['id'];
            $quantity = (int)$_POST['quantity'];
            if ($quantity > 0) $_SESSION['cart'][$key]['quantity'] = $quantity;
            else unset($_SESSION['cart'][$key]);
            session_write_close();
            $this->redirect('cart/index');
        }
    }

    public function remove($key) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['cart'][$key]);
        session_write_close();
        $this->redirect('cart/index');
    }

    public function clear() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);
        session_write_close();
        $this->redirect('cart/index');
    }
}