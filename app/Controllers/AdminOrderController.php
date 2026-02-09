<?php

class AdminOrderController extends AdminController {

    public function index() {
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
        $search = $_GET['search'] ?? '';
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit  = 10; 

        $orderModel = $this->model('Order');

        $result = $orderModel->paginateAllOrders($page, $limit, $status, $search);

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

    public function statistics() {
        $orderModel = $this->model('Order');
        
        $overview = $orderModel->getRevenueStats();
        $dbData = $orderModel->getRevenueLast7Days();
        $topProducts = $orderModel->getTopSellingProducts();

        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $found = false;
            
            foreach ($dbData as $row) {
                if ($row['date'] == $date) {
                    $chartData[] = $row;
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $chartData[] = ['date' => $date, 'revenue' => 0];
            }
        }

        $this->view('admin.order.statistics', [
            'title'       => 'Thống kê doanh thu',
            'overview'    => $overview,
            'chartData'   => $chartData, 
            'topProducts' => $topProducts
        ]);
    }
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

        $items = $orderModel->getOrderItems($id);

        $this->view('admin.order.detail', [
            'title' => 'Chi tiết đơn hàng #' . $order['order_code'],
            'order' => $order,
            'items' => $items
        ]);
    }

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['order_id'];
            $status = $_POST['status'];

            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

            try {
                $orderModel = $this->model('Order');

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