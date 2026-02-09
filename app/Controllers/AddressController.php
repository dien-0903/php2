<?php

class AddressController extends Controller {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['error'] = "Vui lòng đăng nhập để truy cập sổ địa chỉ!";
            $this->redirect('auth/login');
            exit;
        }
    }

    public function index() {
        $userId = $_SESSION['user']['id'];
        $addressModel = $this->model('Address');

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        $limit = 5; 

        $result = $addressModel->paginateByUser($userId, $page, $limit);

        $this->view('user.account.address', [
            'title'       => 'Sổ địa chỉ của tôi',
            'user'        => $_SESSION['user'],
            'addresses'   => $result['data'],     
            'totalPages'  => (int)$result['totalPages'],
            'currentPage' => (int)$page,
            'totalCount'  => (int)$result['totalCount']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'user_id'        => $_SESSION['user']['id'],
                'recipient_name' => trim($_POST['recipient_name']),
                'phone'          => trim($_POST['phone']),
                'address'        => trim($_POST['address_full']), 
                'is_default'     => isset($_POST['is_default']) ? 1 : 0
            ];

            try {
                if (empty($data['recipient_name']) || empty($data['phone']) || empty($data['address'])) {
                    throw new Exception("Vui lòng điền đầy đủ thông tin địa chỉ!");
                }

                $this->model('Address')->create($data);
                $_SESSION['success'] = "Đã thêm địa chỉ giao hàng thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi: " . $e->getMessage();
            }
            
            $this->redirect('address/index');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $addressModel = $this->model('Address');

            $check = $addressModel->query("SELECT user_id FROM addresses WHERE id = ?", [$id])->fetch();
            
            if (!$check || $check['user_id'] != $userId) {
                $_SESSION['error'] = "Hành động không hợp lệ!";
                $this->redirect('address/index');
                return;
            }

            $data = [
                'recipient_name' => trim($_POST['recipient_name']),
                'phone'          => trim($_POST['phone']),
                'address'        => trim($_POST['address_full']),
                'is_default'     => isset($_POST['is_default']) ? 1 : 0
            ];

            try {
                $addressModel->update($id, $data);
                $_SESSION['success'] = "Thông tin địa chỉ đã được cập nhật!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi khi lưu dữ liệu cập nhật!";
            }
            
            $this->redirect('address/index');
        }
    }

    public function setDefault($id) {
        $userId = $_SESSION['user']['id'];
        $addressModel = $this->model('Address');

        $check = $addressModel->query("SELECT user_id FROM addresses WHERE id = ?", [$id])->fetch();
        if ($check && $check['user_id'] == $userId) {
            $addressModel->setDefault($id, $userId);
            $_SESSION['success'] = "Đã thay đổi địa chỉ nhận hàng mặc định!";
        }

        $this->redirect('address/index');
    }

    public function destroy($id) {
        $userId = $_SESSION['user']['id'];
        $addressModel = $this->model('Address');

        $check = $addressModel->query("SELECT user_id FROM addresses WHERE id = ?", [$id])->fetch();
        if ($check && $check['user_id'] == $userId) {
            $addressModel->delete($id);
            $_SESSION['success'] = "Địa chỉ đã được xóa khỏi sổ địa chỉ.";
        } else {
            $_SESSION['error'] = "Bạn không có quyền xóa địa chỉ này!";
        }

        $this->redirect('address/index');
    }
}