<?php

class ProductController extends Controller {

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        $productModel = $this->model('Product');
        $categoryModel = $this->model('Category'); 

        $search = $_GET['search'] ?? '';
        $sort   = $_GET['sort'] ?? 'newest';
        $categoryId = $_GET['category'] ?? '';
        
        $minPrice = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
        $maxPrice = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (int)$_GET['max_price'] : 0;
        
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit  = 9; 

        $result = $productModel->list($page, $limit, $search, $sort, $categoryId, $minPrice, $maxPrice);

        $categories = $categoryModel->getAll(); 

        // Lấy danh sách ID sản phẩm yêu thích từ Session
        $wishlist = $_SESSION['wishlist'] ?? [];

        $this->view('user.product.index', [
            'title'       => 'Sản phẩm - MD',
            'products'    => $result['data'] ?? [],
            'totalPages'  => $result['totalPages'] ?? 0,
            'currentPage' => $page,
            'search'      => $search,
            'sort'        => $sort,
            'currentCat'  => $categoryId,
            'categories'  => $categories,
            'minPrice'    => $minPrice,
            'maxPrice'    => $maxPrice,
            'wishlist'    => $wishlist // Truyền mảng wishlist xuống view
        ]);
    }

    public function show($id) {
        if (!$id) {
            $this->redirect('product/index');
            return;
        }

        if (session_status() === PHP_SESSION_NONE) session_start();

        $productModel = $this->model('Product');
        $variantModel = $this->model('ProductVariant');

        $product = $productModel->show($id);

        if (!$product) {
            $this->notfound("Sản phẩm không tồn tại.");
            return;
        }

        // --- MỚI: Lấy thêm ảnh thư viện (Gallery) để hiện lên giao diện ---
        $product['gallery'] = $this->model('Model')->query(
            "SELECT id, image FROM product_images WHERE product_id = ?", 
            [$id]
        )->fetchAll();

        $variants = $variantModel->getByProduct($id);

        if (!isset($_SESSION['recent_viewed'])) {
            $_SESSION['recent_viewed'] = [];
        }
        if (($key = array_search($id, $_SESSION['recent_viewed'])) !== false) {
            unset($_SESSION['recent_viewed'][$key]);
        }
        array_unshift($_SESSION['recent_viewed'], $id);
        $_SESSION['recent_viewed'] = array_slice($_SESSION['recent_viewed'], 0, 6);

        $recentProducts = [];
        if (!empty($_SESSION['recent_viewed'])) {
            $recentProducts = $productModel->getByIds($_SESSION['recent_viewed']);
        }

        $relatedProducts = $productModel->getRelated($product['category_id'], $id, 4);

        $this->view('user.product.detail', [
            'title'           => $product['name'] . ' - MD',
            'product'         => $product,
            'variants'        => $variants,
            'recentProducts'  => $recentProducts,
            'relatedProducts' => $relatedProducts
        ]);
    }
}