<?php

class OrderController extends Controller {

    public function __construct() {
        // Khởi tạo session nếu chưa được bắt đầu
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        // Kiểm tra đăng nhập: Chỉ người dùng đã đăng nhập mới có thể quản lý đơn hàng
        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để thực hiện thao tác này!";
            $this->redirect('auth/login');
            exit;
        }
    }

    /**
     * HIỂN THỊ LỊCH SỬ ĐƠN HÀNG
     * Tích hợp Phân trang, Tìm kiếm mã đơn/tên khách và Lọc theo trạng thái
     */
    public function history() {
        $userId = $_SESSION['user']['id'];
        $orderModel = $this->model('Order');
        
        // 1. Nhận tham số từ URL (GET)
        $search = $_GET['search'] ?? '';
        $status = (isset($_GET['status']) && $_GET['status'] !== '') ? $_GET['status'] : null;
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit  = 5; // Số lượng đơn hàng tối đa trên mỗi trang

        // 2. Gọi logic phân trang từ Model
        $result = $orderModel->paginateByUser($userId, $page, $limit, $status, $search);

        // 3. Truyền dữ liệu ra View
        $this->view('user.order.history', [
            'title'       => 'Lịch sử mua hàng của tôi',
            'orders'      => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search,
            'status'      => $status
        ]);
    }

    /**
     * TRANG THANH TOÁN (CHECKOUT)
     */
    public function checkout() {
        // Nếu giỏ hàng trống, không cho vào trang thanh toán
        if (empty($_SESSION['cart'])) {
            $this->redirect('cart/index');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $addressModel = $this->model('Address');
        
        // Lấy danh sách địa chỉ của người dùng để chọn nhanh
        $addresses = $addressModel->getByUser($userId);
        
        // Tính toán lại số tiền (bao gồm cả mã giảm giá nếu có trong Session)
        $cart = $_SESSION['cart'];
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = 0;
        $coupon = $_SESSION['coupon'] ?? null;
        if ($coupon) {
            $discount = ($coupon['type'] === 'percent') ? ($subtotal * $coupon['value'] / 100) : $coupon['value'];
            // Giảm giá không được vượt quá tiền hàng
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

    /**
     * XỬ LÝ ĐẶT HÀNG (PLACE ORDER)
     * Nhận dữ liệu POST từ Checkout và gọi Model xử lý Transaction
     */
    public function placeOrder() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $cart = $_SESSION['cart'] ?? [];

            if (empty($cart)) {
                $this->redirect('product/index');
                return;
            }

            // 1. Tính toán giá trị đơn hàng thực tế
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

            // 2. Chuẩn bị dữ liệu đơn hàng
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
                
                // createOrder trong Model đã tích hợp sẵn logic kiểm tra tồn kho & trừ kho
                $orderId = $orderModel->createOrder($orderData, $cart);

                if ($orderId) {
                    // Dọn dẹp session giỏ hàng và voucher sau khi đặt thành công
                    unset($_SESSION['cart']);
                    unset($_SESSION['coupon']); 
                    
                    $_SESSION['success'] = "Đặt hàng thành công! Mã đơn của bạn: #" . $orderData['code'];
                    $this->redirect('order/history');
                }
            } catch (Exception $e) {
                // Bắt lỗi nếu hết hàng đột ngột hoặc lỗi database
                $_SESSION['error'] = "Lỗi đặt hàng: " . $e->getMessage();
                $this->redirect('order/checkout');
            }
        }
    }

    /**
     * CHI TIẾT ĐƠN HÀNG
     */
    public function detail($id = null) {
        if (!$id) {
            $this->redirect('order/history');
            return;
        }

        $orderModel = $this->model('Order');
        $order = $orderModel->show($id);

        // Bảo mật: Kiểm tra đơn hàng có tồn tại và thuộc về user này không
        if (!$order || $order['user_id'] != $_SESSION['user']['id']) {
            $_SESSION['error'] = "Đơn hàng không tồn tại hoặc bạn không có quyền truy cập!";
            $this->redirect('order/history');
            return;
        }

        // Lấy danh sách sản phẩm trong đơn hàng từ bảng order_items
        $items = $orderModel->getOrderItems($id);

        $this->view('user.order.detail', [
            'title' => 'Chi tiết đơn hàng #' . $order['order_code'],
            'order' => $order,
            'items' => $items
        ]);
    }

    /**
     * HỦY ĐƠN HÀNG
     * Chỉ cho phép khi trạng thái đơn là 0 (Chờ xử lý). 
     * Sẽ gọi hàm updateStatus trong Model để tự động HOÀN LẠI TỒN KHO.
     */
    public function cancel($id) {
        $orderModel = $this->model('Order');
        $order = $orderModel->show($id);

        if ($order && $order['user_id'] == $_SESSION['user']['id'] && $order['status'] == 0) {
            try {
                // Trạng thái 4 là Canceled (Đã hủy)
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

    /**
     * MUA LẠI ĐƠN HÀNG (REORDER)
     * Thêm tất cả sản phẩm cũ từ một đơn hàng cũ vào giỏ hàng hiện tại
     */
    public function reorder($id) {
        $orderModel = $this->model('Order');
        $items = $orderModel->getOrderItems($id);

        if (!empty($items)) {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            
            foreach ($items as $item) {
                // Tạo key cho giỏ hàng (ProductID_VariantID)
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