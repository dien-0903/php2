<?php

class OrderController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để thực hiện thao tác này!";
            $this->redirect('auth/login');
            exit;
        }
    }

    public function history() {
        $userId = $_SESSION['user']['id'];
        $orderModel = $this->model('Order');
        
        $search = $_GET['search'] ?? '';
        $status = (isset($_GET['status']) && $_GET['status'] !== '') ? $_GET['status'] : null;
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit  = 5;
        $result = $orderModel->paginateByUser($userId, $page, $limit, $status, $search);

        $this->view('user.order.history', [
            'title'       => 'Lịch sử mua hàng của tôi',
            'orders'      => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search,
            'status'      => $status
        ]);
    }

    public function checkout() {
        if (empty($_SESSION['cart'])) {
            $this->redirect('cart/index');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $addressModel = $this->model('Address');
        
        $addresses = $addressModel->getByUser($userId);
        
        $cart = $_SESSION['cart'];
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 0;
        $coupon = $_SESSION['coupon'] ?? null;
        if ($coupon) {
            $discount = ($coupon['type'] === 'percent') ? ($subtotal * $coupon['value'] / 100) : $coupon['value'];
            if ($discount > $subtotal) $discount = $subtotal;
        }

        $this->view('user.order.checkout', [
            'title'     => 'Thanh toán đơn hàng',
            'cart'      => $cart,
            'addresses' => $addresses,
            'subtotal'  => $subtotal,
            'discount'  => $discount,
            'total'     => max(0, $subtotal - $discount),
            'coupon'    => $coupon
        ]);
    }

    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $cart = $_SESSION['cart'] ?? [];

            if (empty($cart)) {
                $this->redirect('product/index');
                return;
            }

            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $discount = 0;
            if (isset($_SESSION['coupon'])) {
                $cp = $_SESSION['coupon'];
                $discount = ($cp['type'] === 'percent') ? ($subtotal * $cp['value'] / 100) : $cp['value'];
            }

            $finalTotal = max(0, $subtotal - $discount);

            $orderData = [
                'code'     => 'MD-' . strtoupper(uniqid()),
                'user_id'  => $userId,
                'total'    => $finalTotal,
                'name'     => trim($_POST['recipient_name']),
                'phone'    => trim($_POST['phone']),
                'address'  => trim($_POST['address_full']),
                'method'   => $_POST['payment_method'] ?? 'cod',
                'note'     => trim($_POST['note'] ?? '')
            ];

            try {
                $orderModel = $this->model('Order');
                
                $orderId = $orderModel->createOrder($orderData, $cart);

                if ($orderId) {
                    unset($_SESSION['cart']);
                    unset($_SESSION['coupon']); 
                    
                    $_SESSION['success'] = "Đặt hàng thành công! Mã đơn của bạn: #" . $orderData['code'];
                    $this->redirect('order/history');
                }
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi đặt hàng: " . $e->getMessage();
                $this->redirect('order/checkout');
            }
        }
    }

    public function detail($id = null) {
        if (!$id) {
            $this->redirect('order/history');
            return;
        }

        $orderModel = $this->model('Order');
        $order = $orderModel->show($id);

        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['error'] = "Đơn hàng không tồn tại hoặc bạn không có quyền truy cập!";
            $this->redirect('order/history');
            return;
        }

        $items = $orderModel->getOrderItems($id);

        $this->view('user.order.detail', [
            'title' => 'Chi tiết đơn hàng #' . $order['order_code'],
            'order' => $order,
            'items' => $items
        ]);
    }

    public function cancel($id) {
        $orderModel = $this->model('Order');
        $order = $orderModel->show($id);

        if ($order && $order['user_id'] == $_SESSION['user']['id'] && $order['status'] == 0) {
            try {
                $orderModel->updateStatus($id, 4);
                $_SESSION['success'] = "Đã hủy đơn hàng thành công. Sản phẩm đã được hoàn lại kho!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi khi hủy đơn: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Không thể hủy đơn hàng này (Có thể đơn đã được xác nhận).";
        }
        $this->redirect('order/history');
    }

    public function reorder($id) {
        $orderModel = $this->model('Order');
        $items = $orderModel->getOrderItems($id);

        if (!empty($items)) {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            
            foreach ($items as $item) {
                $cartKey = $item['product_id'] . ($item['variant_id'] ? '_' . $item['variant_id'] : '');
                
                $_SESSION['cart'][$cartKey] = [
                    'id'           => $item['product_id'],
                    'variant_id'   => $item['variant_id'],
                    'name'         => $item['product_name'],
                    'price'        => $item['price'],
                    'image'        => $item['product_image'],
                    'quantity'     => $item['quantity'],
                    'variant_info' => $item['variant_info']
                ];
            }
            $_SESSION['success'] = "Sản phẩm từ đơn cũ đã được thêm vào giỏ hàng!";
            $this->redirect('cart/index');
        } else {
            $this->redirect('order/history');
        }
    }
}