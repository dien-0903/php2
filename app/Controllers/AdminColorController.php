<?php

class AdminColorController extends AdminController {
    
    public function index() {
        $colorModel = $this->model('Color');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 5;

        $result = $colorModel->paginate('colors', $page, $limit, $search, [], 'name');

        $this->view('admin.color.index', [
            'title'       => 'Quản lý Màu sắc',
            'colors'      => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $hexCode = trim($_POST['hex_code'] ?? '');
            $model = $this->model('Color');

            if (empty($name)) {
                $_SESSION['error'] = "Tên màu sắc không được để trống!";
                $this->redirect('admincolor/index');
                return;
            }

            if ($model->query("SELECT id FROM colors WHERE name = ? AND deleted_at IS NULL", [$name])->fetch()) {
                $_SESSION['error'] = "Màu sắc '$name' đã tồn tại!";
                $this->redirect('admincolor/index');
                return;
            }

            $model->create(['name' => $name, 'hex_code' => $hexCode]);
            $_SESSION['success'] = "Đã thêm màu mới thành công!";
            session_write_close();
            $this->redirect('admincolor/index');
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $hexCode = trim($_POST['hex_code'] ?? '');

            $this->model('Color')->query(
                "UPDATE colors SET name = ?, hex_code = ?, updated_at = NOW() WHERE id = ?",
                [$name, $hexCode, $id]
            );

            $_SESSION['success'] = "Cập nhật màu sắc thành công!";
            session_write_close();
            $this->redirect('admincolor/index');
        }
    }

    public function destroy($id) {
        $this->model('Color')->delete($id);
        $_SESSION['success'] = "Đã xóa màu sắc khỏi danh mục!";
        session_write_close();
        $this->redirect('admincolor/index');
    }
}