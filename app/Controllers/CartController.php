<?php

class CartController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function index() {
        $cart = $_SESSION['cart'] ?? [];
        $productModel = $this->model('Product');
        $baseModel = $this->model('Model'); 
        foreach ($cart as $key => &$item) {
            $currentStock = 0;
            if (isset($item['variant_id']) && $item['variant_id'] > 0) {
                $variant = $baseModel->query("SELECT stock FROM product_variants WHERE id = ?", [$item['variant_id']])->fetch();
                if ($variant) $currentStock = $variant['stock'];
            } else {
                $prod = $productModel->show($item['id']);
                if ($prod) $currentStock = $prod['stock'];
            }
            $item['stock'] = (int)$currentStock;
            if ($item['quantity'] > $item['stock']) {
                $item['quantity'] = $item['stock'];
            }
        }
        unset($item);
        $_SESSION['cart'] = $cart;

        $subtotal = 0;
        foreach($cart as $item) { $subtotal += $item['price'] * $item['quantity']; }

        $discount = 0;
        $coupon = $_SESSION['coupon'] ?? null;
        if ($coupon) {
            $discount = ($coupon['type'] === 'percent') ? ($subtotal * $coupon['value'] / 100) : $coupon['value'];
            if ($discount > $subtotal) $discount = $subtotal;
        }

        $this->view('user.cart.index', [
            'title'    => 'Giỏ hàng của bạn',
            'cart'     => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total'    => $subtotal - $discount,
            'coupon'   => $coupon
        ]);
    }

    public function add($productId, $variantId = null) {
        $productModel = $this->model('Product');
        $baseModel = $this->model('Model');
        
        if ($variantId) {
            $data = $baseModel->query("SELECT p.name, p.image, pv.price, pv.stock 
                                     FROM product_variants pv 
                                     JOIN products p ON pv.product_id = p.id 
                                     WHERE pv.id = ?", [$variantId])->fetch();
            $variantInfo = "Biến thể chọn"; 
        } else {
            $data = $productModel->show($productId);
            $variantInfo = "Mặc định";
        }

        if (!$data || $data['stock'] <= 0) {
            $_SESSION['error'] = "Sản phẩm đã hết hàng!";
            $this->redirect('product/index');
            return;
        }

        $key = $productId . ($variantId ? '_' . $variantId : '');
        
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        $currentInCart = $_SESSION['cart'][$key]['quantity'] ?? 0;
        if ($currentInCart + 1 > $data['stock']) {
            $_SESSION['error'] = "Sản phẩm này chỉ còn " . $data['stock'] . " món trong kho!";
        } else {
            if (isset($_SESSION['cart'][$key])) {
                $_SESSION['cart'][$key]['quantity']++;
            } else {
                $_SESSION['cart'][$key] = [
                    'id'           => $productId,
                    'variant_id'   => $variantId,
                    'name'         => $data['name'],
                    'price'        => $data['price'],
                    'image'        => $data['image'],
                    'quantity'     => 1,
                    'stock'        => $data['stock'],
                    'variant_info' => $variantInfo
                ];
            }
            $_SESSION['success'] = "Đã thêm vào giỏ hàng!";
        }

        $this->redirect('cart/index');
    }

    public function updateQuantity() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $key = $_POST['id'];
            $quantity = (int)$_POST['quantity'];
            if (isset($_SESSION['cart'][$key])) {
                $stock = $_SESSION['cart'][$key]['stock'];
                if ($quantity > $stock) {
                    $quantity = $stock;
                    $_SESSION['error'] = "Đã đạt giới hạn tồn kho!";
                }
                if ($quantity > 0) $_SESSION['cart'][$key]['quantity'] = $quantity;
                else unset($_SESSION['cart'][$key]);
            }
            $this->redirect('cart/index');
        }
    }

    public function remove($key) {
        unset($_SESSION['cart'][$key]);
        $this->redirect('cart/index');
    }
}