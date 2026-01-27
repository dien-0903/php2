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
            if ($coupon['type'] === 'percent') {
                $discount = ($subtotal * $coupon['value']) / 100;
            } else {
                $discount = $coupon['value'];
            }
        }

        if ($discount > $subtotal) $discount = $subtotal;
        
        $total = $subtotal - $discount;

        $this->view('user.cart.index', [
            'title'    => 'Giỏ hàng của bạn - TechMart',
            'cart'     => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'coupon'   => $coupon,
            'total'    => $total
        ]);
    }

    public function add($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $productModel = $this->model('Product');
        $product = $productModel->show($id);

        if ($product) {
            if (!isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id] = [
                    'id'       => $product['id'],
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'image'    => $product['image'],
                    'quantity' => 1
                ];
            } else {
                $_SESSION['cart'][$id]['quantity']++;
            }
            
            $_SESSION['success'] = "Đã thêm '" . $product['name'] . "' vào giỏ hàng!";
            
            session_write_close();
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/product/index');
        header("Location: " . $referer);
        exit();
    }

    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $id = $_POST['id'];
            $quantity = (int)$_POST['quantity'];

            if ($quantity > 0) {
                $_SESSION['cart'][$id]['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][$id]);
            }
            
            session_write_close();
            $this->redirect('cart/index');
        }
    }

    public function applyCoupon() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
            $couponModel = $this->model('Coupon');
            
            $coupon = $couponModel->findByCode($code);

            if ($coupon && (int)$coupon['status'] === 1) {
                $_SESSION['applied_coupon'] = $coupon;
                $_SESSION['success'] = "Áp dụng mã '" . $coupon['code'] . "' thành công!";
            } else {
                $_SESSION['error'] = "Mã giảm giá không chính xác hoặc đã hết hạn!";
            }

            session_write_close();
            $this->redirect('cart/index');
        }
    }

    public function removeCoupon() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['applied_coupon']);
        $_SESSION['success'] = "Đã hủy bỏ mã giảm giá.";
        session_write_close();
        $this->redirect('cart/index');
    }

    public function remove($id) {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['cart'][$id]);
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