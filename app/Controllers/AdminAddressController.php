<?php

class AdminAddressController extends AdminController {
    
    public function index($userId = null) {
        $userModel = $this->model('User');
        
        if (!$userId) {
            $_SESSION['error'] = 'Không tìm thấy ID người dùng để xem địa chỉ!';
            $this->redirect('adminuser/index');
            return;
        }

        $user = $userModel->show($userId);
        if (!$user) {
            $_SESSION['error'] = 'Người dùng không tồn tại trên hệ thống!';
            $this->redirect('adminuser/index');
            return;
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        $limit = 5; 

        $addressModel = $this->model('Address');
        $result = $addressModel->paginateByUser($userId, $page, $limit);

        $this->view('admin.address.index', [
            'title'       => 'Quản lý sổ địa chỉ: ' . $user['fullname'],
            'user'        => $user,
            'addresses'   => $result['data'],      
            'totalPages'  => $result['totalPages'], 
            'currentPage' => $page,                
            'totalCount'  => $result['totalCount']
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $userId = $_POST['user_id'];
            $recipientName = trim($_POST['recipient_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? ''); 
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($recipientName) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin người nhận và địa chỉ chi tiết!";
                $this->redirect('adminaddress/index/' . $userId);
                return;
            }

            try {
                $this->model('Address')->create([
                    'user_id'        => $userId,
                    'recipient_name' => $recipientName,
                    'phone'          => $phone,
                    'address'        => $address,
                    'is_default'     => $isDefault,
                    'status'         => 1 
                ]);

                $_SESSION['success'] = "Đã thêm địa chỉ mới cho khách hàng thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống khi lưu địa chỉ: " . $e->getMessage();
            }

            $this->redirect('adminaddress/index/' . $userId);
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();
            
            $userId = $_POST['user_id'];
            $recipientName = trim($_POST['recipient_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $isDefault = isset($_POST['is_default']) ? 1 : 0;

            if (empty($recipientName) || empty($phone) || empty($address)) {
                $_SESSION['error'] = "Thông tin cập nhật không được để trống bất kỳ trường nào!";
                $this->redirect('adminaddress/index/' . $userId);
                return;
            }

            try {
                $this->model('Address')->update($id, [
                    'recipient_name' => $recipientName,
                    'phone'          => $phone,
                    'address'        => $address,
                    'is_default'     => $isDefault
                ]);

                $_SESSION['success'] = "Thông tin địa chỉ đã được cập nhật thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi phát sinh khi cập nhật dữ liệu: " . $e->getMessage();
            }

            $this->redirect('adminaddress/index/' . $userId);
        }
    }

    public function set_default($id, $userId) {
        try {
            $this->model('Address')->setDefault($id, $userId);
            $_SESSION['success'] = "Đã thay đổi địa chỉ nhận hàng mặc định mới!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Không thể thiết lập địa chỉ mặc định!";
        }
        
        $this->redirect('adminaddress/index/' . $userId);
    }

    public function toggle_status($id, $userId) {
        try {
            $this->model('Address')->toggleStatus($id);
            $_SESSION['success'] = "Đã cập nhật trạng thái hoạt động của địa chỉ!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Thao tác thay đổi trạng thái thất bại!";
        }

        $this->redirect('adminaddress/index/' . $userId);
    }

    public function destroy($id, $userId) {
        try {
            $this->model('Address')->delete($id);
            $_SESSION['success'] = "Địa chỉ đã được gỡ bỏ khỏi hệ thống!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi thực hiện xóa địa chỉ!";
        }
        
        $this->redirect('adminaddress/index/' . $userId);
    }
}