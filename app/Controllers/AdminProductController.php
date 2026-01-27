<?php

class AdminProductController extends AdminController {
    
    public function index() {
        $productModel = $this->model('Product');
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = $_GET['search'] ?? '';
        $limit = 8; 

        $result = $productModel->list($page, $limit, $search);
        
        $categories = $this->model('Category')->getAll();
        $brands = $this->model('Brand')->getAll();
        
        $this->view('admin.Product.index', [
            'title'          => 'Hệ thống Quản lý Sản phẩm',
            'products'       => $result['data'],
            'totalPages'     => $result['totalPages'],
            'currentPage'    => $page,
            'search'         => $search,
            'all_categories' => $categories, 
            'all_brands'     => $brands
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $category_id = $_POST['category_id'] ?: null;
            $brand_id = $_POST['brand_id'] ?: null;

            if (empty($name) || $price <= 0) {
                $_SESSION['error'] = "Tên sản phẩm không được để trống và giá phải lớn hơn 0!";
                $this->handleError('add', $_POST);
                return;
            }

            if ($price > 999999999) {
                $_SESSION['error'] = "Giá tiền quá lớn! Vui lòng nhập giá nhỏ hơn 1 tỷ VNĐ.";
                $this->handleError('add', $_POST);
                return;
            }

            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) $imageName = $uploaded;
            }

            try {
                $this->model('Product')->create([
                    'name'        => $name,
                    'price'       => $price,
                    'image'       => $imageName,
                    'category_id' => $category_id,
                    'brand_id'    => $brand_id
                ]);

                $_SESSION['success'] = "Sản phẩm '$name' đã được niêm yết thành công!";
                session_write_close();
                $this->redirect('adminproduct/index');
            } catch (PDOException $e) {
                if ($e->getCode() == '22003') {
                    $_SESSION['error'] = "Lỗi: Số tiền nhập vào vượt quá giới hạn cho phép của hệ thống!";
                } else {
                    $_SESSION['error'] = "Lỗi cơ sở dữ liệu: " . $e->getMessage();
                }
                $this->handleError('add', $_POST);
            }
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) session_start();

            $model = $this->model('Product');
            $currentProduct = $model->show($id);
            
            if (!$currentProduct) {
                $_SESSION['error'] = "Không tìm thấy sản phẩm!";
                $this->redirect('adminproduct/index');
                return;
            }

            $name = trim($_POST['name'] ?? '');
            $price = (float)($_POST['price'] ?? 0);

            if (empty($name) || $price <= 0) {
                $_SESSION['error'] = "Dữ liệu cập nhật không hợp lệ (Tên trống hoặc giá <= 0)!";
                $this->handleError('edit', $_POST);
                return;
            }

            if ($price > 999999999) {
                $_SESSION['error'] = "Giá tiền quá lớn! Giới hạn cho phép là dưới 1 tỷ VNĐ.";
                $this->handleError('edit', $_POST);
                return;
            }

            $imageName = $currentProduct['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image']);
                if ($uploaded) $imageName = $uploaded;
            }

            try {
                $model->update($id, [
                    'name'        => $name,
                    'price'       => $price,
                    'image'       => $imageName,
                    'category_id' => $_POST['category_id'] ?: null,
                    'brand_id'    => $_POST['brand_id'] ?: null
                ]);

                $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
                session_write_close();
                $this->redirect('adminproduct/index');
            } catch (PDOException $e) {
                if ($e->getCode() == '22003') {
                    $_SESSION['error'] = "Lỗi: Giá tiền quá lớn không thể lưu vào hệ thống!";
                } else {
                    $_SESSION['error'] = "Lỗi phát sinh: " . $e->getMessage();
                }
                $this->handleError('edit', $_POST);
            }
        }
    }

    private function handleError($type, $postData) {
        $_SESSION['error_type'] = $type; 
        $_SESSION['old'] = $postData;    
        session_write_close();
        $this->redirect('adminproduct/index');
    }

    public function destroy($id) {
        $this->model('Product')->delete($id);
        $_SESSION['success'] = "Sản phẩm đã được xóa khỏi hệ thống!";
        session_write_close();
        $this->redirect('adminproduct/index');
    }

    private function uploadFile($file) {
        $targetDir = BASE_PATH . "/public/uploads/products/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            $fileName = "techmart_" . time() . "_" . uniqid() . "." . $ext;
            if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                return $fileName;
            }
        }
        return null;
    }
}