<?php

class CouponController extends Controller {
    
    
    public function index() {
        $couponModel = $this->model('Coupon');

        $search = $_GET['search'] ?? '';
        $page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit  = 9; 
        $result = $couponModel->paginate(
            'coupons',    
            $page,         
            $limit,        
            $search,      
            ['status' => 1], 
            'code'         
        );

        
        if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'     => true,
                'data'        => $result['data'],
                'totalPages'  => $result['totalPages'],
                'currentPage' => $page
            ]);
            exit; 
        }

        $this->view('user.coupon.index', [
            'coupons'     => $result['data'],
            'totalPages'  => $result['totalPages'],
            'currentPage' => $page,
            'search'      => $search,
            'title'       => 'Kho Voucher TechMart - Săn Ưu Đãi'
        ]);
    }
    public function check($code) {
        header('Content-Type: application/json');
        $couponModel = $this->model('Coupon');
        $coupon = $couponModel->findByCode($code);

        if ($coupon && $coupon['status'] == 1) {
            echo json_encode(['success' => true, 'data' => $coupon]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn!']);
        }
    }
}