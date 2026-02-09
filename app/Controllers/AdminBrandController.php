<?php

class AdminBrandController extends AdminController {
    
    public function index() {
        $brandModel = $this->model('Brand');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 8; 
        $result = $brandModel->list($page, $limit, $search);
        
        $this->view('admin.brand.index', [
            'title'       => 'Quản lý Thương hiệu',
            'brands'      => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $model = $this->model('Brand');

            if (empty($name)) {
                $_SESSION['error'] = "Vui lòng nhập tên thương hiệu!";
                $this->handleError('add', $_POST);
                return;
            }
            if (method_exists($model, 'exists') && $model->exists($name)) {
                $_SESSION['error'] = "Thương hiệu '$name' đã tồn tại!";
                $this->handleError('add', $_POST);
                return;
            }

            $logoName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image'], 'brands');
                if ($uploaded) {
                    $logoName = $uploaded;
                } else {
                    $_SESSION['error'] = "Lỗi: Không thể lưu file ảnh vào thư mục uploads/brands/";
                    $this->handleError('add', $_POST);
                    return;
                }
            }

            try {
                $model->create([
                    'name'        => $name,
                    'description' => trim($_POST['description'] ?? ''),
                    'image'       => $logoName
                ]);

                $_SESSION['success'] = "Đã thêm thương hiệu mới thành công!";
                session_write_close();
                $this->redirect('adminbrand/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi Database: " . $e->getMessage();
                $this->handleError('add', $_POST);
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = $this->model('Brand');
            
            $current = method_exists($model, 'show') ? $model->show($id) : null;
            
            if (!$current) {
                $_SESSION['error'] = "Dữ liệu không tồn tại!";
                $this->redirect('adminbrand/index');
                return;
            }

            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = "Tên không được để trống!";
                $this->handleError('edit', $_POST);
                return;
            }

            $logoName = $current['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image'], 'brands');
                if ($uploaded) {
                    $logoName = $uploaded;
                }
            }

            try {
                if (method_exists($model, 'update')) {
                    $model->update($id, [
                        'name'        => $name,
                        'description' => trim($_POST['description'] ?? ''),
                        'image'       => $logoName
                    ]);
                    $_SESSION['success'] = "Cập nhật thành công thương hiệu!";
                } else {
                    $_SESSION['error'] = "Lỗi: Model Brand thiếu phương thức update()";
                }
                
                session_write_close();
                $this->redirect('adminbrand/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi khi cập nhật: " . $e->getMessage();
                $this->handleError('edit', $_POST);
            }
        }
    }

    public function destroy($id) {
        $this->model('Brand')->delete($id);
        $_SESSION['success'] = "Đã xóa thương hiệu khỏi hệ thống!";
        session_write_close();
        $this->redirect('adminbrand/index');
    }

    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type; 
        $_SESSION['old'] = $postData;    
        session_write_close();
        $this->redirect('adminbrand/index');
    }

}