<?php

class AdminCategoryController extends AdminController {

    public function index() {
        $model = $this->model('Category');
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        
        $result = $model->list($page, 8, $search);

        $this->view('admin.category.index', [
            'categories'  => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search,
            'title'       => 'Quản lý Danh mục'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $name = trim($_POST['name'] ?? '');
            $model = $this->model('Category');

            if (empty($name)) {
                $_SESSION['error'] = "Vui lòng nhập tên danh mục!";
                $this->handleError('add', $_POST);
                return;
            }
            if ($model->isDuplicate($name)) {
                $_SESSION['error'] = "Danh mục '$name' đã tồn tại!";
                $this->handleError('add', $_POST);
                return;
            }

            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // SỬ DỤNG HÀM TỪ ADMIN CONTROLLER (Class Cha)
                // Tham số 'categories' để lưu vào thư mục public/uploads/categories/
                $uploaded = $this->uploadFile($_FILES['image'], 'categories');
                if ($uploaded) {
                    $imageName = $uploaded;
                }
            }

            try {
                $model->create([
                    'name'  => $name, 
                    'image' => $imageName
                ]);

                $_SESSION['success'] = "Đã thêm danh mục mới thành công!";
                session_write_close();
                $this->redirect('admincategory/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
                $this->handleError('add', $_POST);
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $model = $this->model('Category');
            $current = $model->show($id);
            
            if (!$current) {
                $_SESSION['error'] = "Không tìm thấy dữ liệu danh mục!";
                $this->redirect('admincategory/index');
                return;
            }

            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $_SESSION['error'] = "Tên danh mục không được để trống!";
                $this->handleError('edit', $_POST);
                return;
            }

            if ($model->isDuplicate($name, $id)) {
                $_SESSION['error'] = "Tên danh mục này đã được sử dụng!";
                $this->handleError('edit', $_POST);
                return;
            }

            $imageName = $current['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // SỬ DỤNG HÀM TỪ ADMIN CONTROLLER
                $uploaded = $this->uploadFile($_FILES['image'], 'categories');
                if ($uploaded) {
                    $imageName = $uploaded;
                }
            }

            try {
                $model->update($id, [
                    'name'  => $name, 
                    'image' => $imageName
                ]);

                $_SESSION['success'] = "Cập nhật danh mục thành công!";
                session_write_close();
                $this->redirect('admincategory/index');
            } catch (PDOException $e) {
                $_SESSION['error'] = "Lỗi cập nhật: " . $e->getMessage();
                $this->handleError('edit', $_POST);
            }
        }
    }

    public function destroy($id) {
        $this->model('Category')->delete($id);
        $_SESSION['success'] = "Đã xóa danh mục khỏi hệ thống!";
        session_write_close();
        $this->redirect('admincategory/index');
    }

    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type;
        $_SESSION['old'] = $postData;
        session_write_close();
        $this->redirect('admincategory/index');
    }

    // ĐÃ XÓA hàm uploadFile() private tại đây
}