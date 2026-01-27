<!DOCTYPE html>

<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Quản trị hệ thống' }} - TechMart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-admin {
            background-color: #0f172a;
            border-bottom: 3px solid #10b981;
            padding: 10px 0;
        }

        .navbar-brand {
            color: #10b981 !important;
            letter-spacing: -0.5px;
        }

        .nav-link {
            color: #cbd5e1 !important;
            font-weight: 600;
            padding: 8px 16px !important;
            transition: 0.2s;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #10b981 !important;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 8px;
        }

        .btn-view-site {
            border: 1px solid #10b981;
            color: #10b981;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-view-site:hover {
            background: #10b981;
            color: #fff;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
    </style>


</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-admin sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="{{ BASE_URL }}/adminproduct/index">
                <i class="bi bi-shield-lock-fill me-2"></i>ADMIN MD
            </a>

            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#adminMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ BASE_URL }}/adminproduct/index">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ BASE_URL }}/admincategory/index">Danh mục</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ BASE_URL }}/adminbrand/index">Thương hiệu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ BASE_URL }}/adminuser/index">Thành viên</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ BASE_URL }}/admincoupon/index">Coupon</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ BASE_URL }}/admincolor/index">Màu sắc</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ BASE_URL }}/adminsize/index">Size</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="btn btn-view-site px-3" href="{{ BASE_URL }}/product/index" target="_blank">
                            <i class="bi bi-box-arrow-up-right me-1"></i>Xem Website
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle bg-white/10 rounded-pill px-4" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-person-fill me-1"></i>{{ $_SESSION['user']['fullname'] ?? 'Admin' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-gear me-2"></i>Cấu hình</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger fw-bold" href="{{ BASE_URL }}/adminauth/logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>


    </nav>


    <div class="container py-5 flex-grow-1">

        @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i>
            <div>{{ $_SESSION['success'] }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['success']) @endphp
        </div>
        @endif

        @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>{{ $_SESSION['error'] }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['error']) @endphp
        </div>
        @endif