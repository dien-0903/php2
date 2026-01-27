<?php

class ProductController extends Controller {

    public function index() {
        $productModel = $this->model('Product');

        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 8; 

        $result = $productModel->list($page, $limit, $search);

        $this->view('user.product.index', [
            'title'       => 'Sản phẩm - TechMart',
            'products'    => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search
        ]);
    }

    public function show($id) {
        if (!$id) {
            $this->redirect('product/index');
            return;
        }

        $productModel = $this->model('Product');
        $variantModel = $this->model('ProductVariant');

        $product = $productModel->show($id);

        if (!$product) {
            $this->notfound("Sản phẩm mà bạn tìm kiếm không tồn tại hoặc đã bị gỡ bỏ.");
            return;
        }

        $variants = $variantModel->getByProduct($id);

        $this->view('user.product.detail', [
            'title'    => $product['name'] . ' - TechMart',
            'product'  => $product,
            'variants' => $variants 
        ]);
    }
}