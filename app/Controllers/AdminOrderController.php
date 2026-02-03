<?php

class AdminOrderController extends AdminController {

    /**
     * Hiển thị danh sách đơn hàng dành cho Admin
     * Hỗ trợ: Phân trang, Tìm kiếm mã đơn/tên khách, Lọc trạng thái
     */
    public function index() {
        // 1. Tiếp nhận các tham số lọc từ URL (GET)
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
        $search = $_GET['search'] ?? '';
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit  = 10; // Số lượng đơn hàng hiển thị trên mỗi trang

        $orderModel = $this->model('Order');

        // 2. Gọi hàm phân trang đã định nghĩa trong Model
        $result = $orderModel->paginateAllOrders($page, $limit, $status, $search);

        // 3. Trả về view kèm các dữ liệu cần thiết
        $this->view('admin.order.index', [
            'title'       => 'Quản lý Đơn hàng',
            'orders'      => $result['data'],
            'totalPages'  => $result['totalPages'],
            'totalCount'  => $result['totalCount'],
            'currentPage' => $page,
            'status'      => $status,
            'search'      => $search
        ]);
    }

    /**
     * Hiển thị chi tiết một đơn hàng
     * @param int $id ID của đơn hàng
     */
    public function show($id = null) {
        if (!$id) {
            $this->redirect('adminorder/index');
            return;
        }

        $orderModel = $this->model('Order');
        $order = $orderModel->show($id); 

        if (!$order) {
            $_SESSION['error'] = "Đơn hàng không tồn tại hoặc đã bị xóa!";
            $this->redirect('adminorder/index');
            return;
        }

        // Lấy danh sách sản phẩm khách đã mua trong đơn này
        $items = $orderModel->getOrderItems($id);

        $this->view('admin.order.detail', [
            'title' => 'Chi tiết đơn hàng #' . $order['order_code'],
            'order' => $order,
            'items' => $items
        ]);
    }

    /**
     * Xử lý cập nhật trạng thái đơn hàng
     * Hỗ trợ cả Request thông thường và AJAX (không load lại trang)
     */
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['order_id'];
            $status = $_POST['status'];

            // Kiểm tra xem đây có phải là yêu cầu AJAX không
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            try {
                $orderModel = $this->model('Order');
                
                /**
                 * Gọi hàm updateStatus trong Model.
                 * Hàm này (như đã viết trong Model) sẽ tự động cộng lại tồn kho nếu đơn bị Hủy (status = 4)
                 */
                $orderModel->updateStatus($id, $status);

                if ($isAjax) {
                    echo json_encode(['status' => 'success', 'message' => 'Đã cập nhật trạng thái và tồn kho thành công!']);
                    exit;
                }

                $_SESSION['success'] = "Cập nhật trạng thái đơn hàng thành công!";
                $this->redirect('adminorder/show/' . $id);

            } catch (Exception $e) {
                if ($isAjax) {
                    http_response_code(400);
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                    exit;
                }

                $_SESSION['error'] = "Lỗi: " . $e->getMessage();
                $this->redirect('adminorder/index');
            }
        }
    }
}