<?php

class AdminVariantController extends AdminController {
    
    public function index($productId = null) {
        $productModel = $this->model('Product');
        
        if (!$productId) {
            return $this->view('admin.variant.index', [
                'title'     => 'Lỗi truy cập',
                'error_msg' => 'Không tìm thấy mã sản phẩm để quản lý biến thể! Vui lòng quay lại danh sách sản phẩm.',
                'product'   => null,
                'variants'  => [],
                'colors'    => [],
                'sizes'     => []
            ]);
        }

        $product = $productModel->show($productId);

        if (!$product) {
            return $this->view('admin.variant.index', [
                'title'     => 'Sản phẩm không tồn tại',
                'error_msg' => "Dữ liệu cho sản phẩm ID #$productId không tìm thấy hoặc đã bị xóa.",
                'product'   => null,
                'variants'  => [],
                'colors'    => [],
                'sizes'     => []
            ]);
        }

        $variantModel = $this->model('ProductVariant');
        $variants = $variantModel->getByProduct($productId);

        $colors = $this->model('Model')->query("SELECT * FROM colors WHERE deleted_at IS NULL")->fetchAll();
        $sizes = $this->model('Model')->query("SELECT * FROM sizes WHERE deleted_at IS NULL")->fetchAll();

        $this->view('admin.variant.index', [
            'title'    => 'Biến thể: ' . $product['name'],
            'product'  => $product,
            'variants' => $variants,
            'colors'   => $colors,
            'sizes'    => $sizes
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            
            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = $this->uploadImage($_FILES['image']);
            }

            $data = [
                'product_id' => $productId,
                'color_id'   => $_POST['color_id'] ?: null,
                'size_id'    => $_POST['size_id'] ?: null,
                'sku'        => trim($_POST['sku']),
                'price'      => (float)$_POST['price'],
                'stock'      => (int)$_POST['stock'],
                'image'      => $imageName
            ];

            try {
                $this->model('ProductVariant')->create($data);
                $_SESSION['success'] = "Đã niêm yết biến thể mới thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi: Mã SKU bị trùng hoặc dữ liệu không hợp lệ!";
            }

            session_write_close();
            $this->redirect('adminvariant/index/' . $productId);
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $variantModel = $this->model('ProductVariant');

            $oldData = $variantModel->query("SELECT image FROM product_variants WHERE id = ?", [$id])->fetch();
            $imageName = $oldData['image'] ?? 'default.jpg';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imageName = $this->uploadImage($_FILES['image']);
            }

            $sql = "UPDATE product_variants SET 
                    color_id = :color_id, 
                    size_id = :size_id, 
                    sku = :sku, 
                    price = :price, 
                    stock = :stock, 
                    image = :image,
                    updated_at = NOW() 
                    WHERE id = :id";

            try {
                $variantModel->query($sql, [
                    'id'       => $id,
                    'color_id' => $_POST['color_id'] ?: null,
                    'size_id'  => $_POST['size_id'] ?: null,
                    'sku'      => trim($_POST['sku']),
                    'price'    => (float)$_POST['price'],
                    'stock'    => (int)$_POST['stock'],
                    'image'    => $imageName
                ]);
                $_SESSION['success'] = "Đã cập nhật thông tin biến thể!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi cập nhật: Vui lòng kiểm tra lại mã SKU!";
            }

            session_write_close();
            $this->redirect('adminvariant/index/' . $productId);
        }
    }

    public function destroy($id, $productId) {
        $this->model('ProductVariant')->delete($id);
        $_SESSION['success'] = "Đã gỡ bỏ biến thể thành công!";
        session_write_close();
        $this->redirect('adminvariant/index/' . $productId);
    }

    private function uploadImage($file) {
        $targetDir = BASE_PATH . "/public/uploads/products/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = "var_" . time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($file['tmp_name'], $targetDir . $fileName);
        return $fileName;
    }
}