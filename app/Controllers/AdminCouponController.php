<?php

class AdminCouponController extends AdminController {
    
    public function index() {
        $couponModel = $this->model('Coupon');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 6; 

        $result = $couponModel->list($page, $limit, $search);

        $this->view('admin.coupon.index', [
            'title'       => 'Quản lý Mã giảm giá',
            'coupons'     => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $code = strtoupper(trim($_POST['code'] ?? ''));
            $value = (float)($_POST['value'] ?? 0);
            $model = $this->model('Coupon');

            if (empty($code) || $value <= 0) {
                $_SESSION['error'] = "Mã không được để trống và giá trị phải lớn hơn 0!";
                $this->handleError('add', $_POST);
                return;
            }

            if ($model->exists($code)) {
                $_SESSION['error'] = "Mã giảm giá '$code' đã tồn tại trong hệ thống!";
                $this->handleError('add', $_POST);
                return;
            }

            try {
                $model->create([
                    'code'   => $code,
                    'type'   => $_POST['type'],
                    'value'  => $value,
                    'status' => isset($_POST['status']) ? 1 : 0
                ]);

                $_SESSION['success'] = "Đã tạo mã ưu đãi '$code' thành công!";
                session_write_close();
                $this->redirect('admincoupon/index');
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
                $this->handleError('add', $_POST);
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $model = $this->model('Coupon');
            $value = (float)($_POST['value'] ?? 0);

            if ($value <= 0) {
                $_SESSION['error'] = "Giá trị giảm giá không hợp lệ!";
                $this->handleError('edit', $_POST);
                return;
            }

            try {
                $model->update($id, [
                    'type'   => $_POST['type'],
                    'value'  => $value,
                    'status' => isset($_POST['status']) ? 1 : 0
                ]);

                $_SESSION['success'] = "Cập nhật mã giảm giá thành công!";
                session_write_close();
                $this->redirect('admincoupon/index');
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi cập nhật dữ liệu!";
                $this->handleError('edit', $_POST);
            }
        }
    }

    public function destroy($id) {
        $this->model('Coupon')->delete($id);
        $_SESSION['success'] = "Đã gỡ bỏ mã giảm giá khỏi hệ thống!";
        session_write_close();
        $this->redirect('admincoupon/index');
    }

    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type;
        $_SESSION['old'] = $postData;
        session_write_close();
        $this->redirect('admincoupon/index');
    }
}