<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MD - Công nghệ hàng đầu' }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar-user { background-color: #0f172a !important; border-bottom: 3px solid #2563eb; padding: 12px 0; }
        .navbar-brand { color: #fff !important; font-weight: 800; letter-spacing: -1.5px; }
        .nav-link { color: #cbd5e1 !important; font-weight: 600; font-size: 0.9rem; transition: 0.2s; padding: 8px 16px !important; }
        .nav-link:hover { color: #3b82f6 !important; }
        
        .user-profile-btn { 
            background: rgba(255, 255, 255, 0.08); 
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff; 
            padding: 6px 18px; 
            border-radius: 50px; 
            font-weight: 600;
            transition: 0.3s;
        }
        .user-profile-btn:hover { background: rgba(255, 255, 255, 0.15); border-color: #3b82f6; }
        
        .dropdown-menu { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.2); 
            margin-top: 15px !important;
            padding: 8px;
            min-width: 220px;
        }
        .dropdown-item { border-radius: 8px; padding: 10px 15px; font-weight: 500; font-size: 0.85rem; }
        .dropdown-item:hover { background-color: #f1f5f9; color: #2563eb; }
        
        .cart-badge { font-size: 0.65rem; padding: 0.35em 0.6em; top: 5px !important; }
        .animate-slide-down { animation: slideDown 0.4s ease-out; }
        @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-user sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fs-3" href="{{ rtrim(BASE_URL, '/') }}/home/index">
            <i class="bi bi-cpu-fill me-2 text-primary"></i>MYDUYEN
        </a>
        
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                <li class="nav-item">
                    <a class="nav-link" href="{{ rtrim(BASE_URL, '/') }}/product/index">SẢN PHẨM</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ rtrim(BASE_URL, '/') }}/coupon/index">VOUCHER</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ rtrim(BASE_URL, '/') }}/wishlist/index">YÊU THÍCH</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ rtrim(BASE_URL, '/') }}/compare/index">SO SÁNH</a>
                </li>
                
                @if(isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin')
                    <li class="nav-item border-start border-light-subtle ps-lg-2 ms-lg-2">
                        <a class="nav-link text-warning fw-bold" href="{{ rtrim(BASE_URL, '/') }}/adminproduct/index">
                            <i class="bi bi-shield-lock-fill me-1"></i>QUẢN TRỊ
                        </a>
                    </li>
                @endif
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <!-- GIỎ HÀNG -->
                <li class="nav-item me-3">
                    <a class="nav-link position-relative d-inline-block px-3" href="{{ rtrim(BASE_URL, '/') }}/cart/index">
                        <i class="bi bi-cart3 fs-5"></i>
                        @if(!empty($_SESSION['cart']))
                            <span class="position-absolute translate-middle badge rounded-pill bg-danger cart-badge">
                                {{ count($_SESSION['cart']) }}
                            </span>
                        @endif
                    </a>
                </li>

                @if(isset($_SESSION['user']) && !empty($_SESSION['user']))
                    <li class="nav-item dropdown">
                        <button class="user-profile-btn dropdown-toggle d-flex align-items-center gap-2" 
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-5 text-primary"></i>
                            <span>{{ $_SESSION['user']['fullname'] }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                            <li class="px-3 py-2 border-bottom mb-2">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Hội viên MD</div>
                                <div class="text-dark fw-bold small text-truncate" style="max-width: 180px;">{{ $_SESSION['user']['email'] }}</div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ rtrim(BASE_URL, '/') }}/order/history">
                                    <i class="bi bi-bag-check me-2"></i>Đơn hàng của tôi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ rtrim(BASE_URL, '/') }}/user/profile">
                                    <i class="bi bi-person me-2"></i>Thông tin cá nhân
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ rtrim(BASE_URL, '/') }}/user/address">
                                    <i class="bi bi-geo-alt me-2"></i>Sổ địa chỉ
                                </a>
                            </li>
                            <li><hr class="dropdown-divider opacity-50"></li>
                            <li>
                                <a class="dropdown-item text-danger fw-bold" href="{{ rtrim(BASE_URL, '/') }}/auth/logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-white" href="{{ rtrim(BASE_URL, '/') }}/auth/login">ĐĂNG NHẬP</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" href="{{ rtrim(BASE_URL, '/') }}/auth/register">
                            ĐĂNG KÝ
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5 flex-grow-1">
    <!-- Hiển thị thông báo Success/Error -->
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center animate-slide-down rounded-4 p-3">
            <i class="bi bi-check-circle-fill me-2 fs-4"></i>
            <div class="fw-bold">{{ $_SESSION['success'] }}</div>
            <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center animate-slide-down rounded-4 p-3">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-4"></i>
            <div class="fw-bold">{{ $_SESSION['error'] }}</div>
            <button type="button" class="btn-close ms-auto shadow-none" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['error']) @endphp
        </div>
    @endif