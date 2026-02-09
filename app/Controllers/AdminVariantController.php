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
            $colorId   = $_POST['color_id'] ?: null;
            $sizeId    = $_POST['size_id'] ?: null;
            $sku       = trim($_POST['sku']);
            $variantModel = $this->model('ProductVariant');

            $checkSku = $variantModel->query("SELECT id FROM product_variants WHERE sku = ?", [$sku])->fetch();
            if ($checkSku) {
                $_SESSION['error'] = "Lỗi: Mã SKU '$sku' đã tồn tại trong hệ thống. Vui lòng chọn mã khác!";
                session_write_close();
                $this->redirect('adminvariant/index/' . $productId);
                return; 
            }

            $checkExists = $variantModel->query(
                "SELECT id FROM product_variants 
                 WHERE product_id = ? AND color_id <=> ? AND size_id <=> ?", 
                [$productId, $colorId, $sizeId]
            )->fetch();

            if ($checkExists) {
                $_SESSION['error'] = "Lỗi: Biến thể với Màu và Size này đã tồn tại cho sản phẩm hiện tại!";
                session_write_close();
                $this->redirect('adminvariant/index/' . $productId);
                return;
            }

            $imageName = 'default.jpg';
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image'], 'products');
                if ($uploaded) $imageName = $uploaded;
            }

            $data = [
                'product_id' => $productId,
                'color_id'   => $colorId,
                'size_id'    => $sizeId,
                'sku'        => $sku,
                'price'      => (float)$_POST['price'],
                'stock'      => (int)$_POST['stock'],
                'image'      => $imageName
            ];

            try {
                $variantModel->create($data);
                $_SESSION['success'] = "Đã niêm yết biến thể mới thành công!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
            }

            session_write_close();
            $this->redirect('adminvariant/index/' . $productId);
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $colorId   = $_POST['color_id'] ?: null;
            $sizeId    = $_POST['size_id'] ?: null;
            $sku       = trim($_POST['sku']);
            $variantModel = $this->model('ProductVariant');

            $checkSku = $variantModel->query(
                "SELECT id FROM product_variants WHERE sku = ? AND id != ?", 
                [$sku, $id]
            )->fetch();

            if ($checkSku) {
                $_SESSION['error'] = "Lỗi: Mã SKU '$sku' đã được sử dụng bởi một biến thể khác!";
                session_write_close();
                $this->redirect('adminvariant/index/' . $productId);
                return;
            }

            $checkExists = $variantModel->query(
                "SELECT id FROM product_variants 
                 WHERE product_id = ? AND color_id <=> ? AND size_id <=> ? AND id != ?", 
                [$productId, $colorId, $sizeId, $id]
            )->fetch();

            if ($checkExists) {
                $_SESSION['error'] = "Lỗi: Đã có một biến thể khác trùng Màu và Size này trong sản phẩm!";
                session_write_close();
                $this->redirect('adminvariant/index/' . $productId);
                return;
            }

            $oldData = $variantModel->query("SELECT image FROM product_variants WHERE id = ?", [$id])->fetch();
            $imageName = $oldData['image'] ?? 'default.jpg';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = $this->uploadFile($_FILES['image'], 'products');
                if ($uploaded) $imageName = $uploaded;
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
                    'color_id' => $colorId,
                    'size_id'  => $sizeId,
                    'sku'      => $sku,
                    'price'    => (float)$_POST['price'],
                    'stock'    => (int)$_POST['stock'],
                    'image'    => $imageName
                ]);
                $_SESSION['success'] = "Đã cập nhật thông tin biến thể!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Lỗi cập nhật: " . $e->getMessage();
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

}