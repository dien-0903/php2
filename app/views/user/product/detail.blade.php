@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
@endphp

@include('user.layouts.header')

<div class="container pb-5 text-dark">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4 animate-bounce-in">
            <div class="d-flex align-items-center text-success fw-bold">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <span>{{ $_SESSION['success'] }}</span>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
        </div>
        @php unset($_SESSION['success']) @endphp
    @endif

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 small">
            <li class="breadcrumb-item">
                <a href="{{ rtrim(BASE_URL, '/') }}/" class="text-decoration-none text-muted">Trang chủ</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="text-decoration-none text-muted">Sản phẩm</a>
            </li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $product['name'] }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-5 shadow-sm border border-slate-50 overflow-hidden mb-5">
        <div class="row g-0">
            <div class="col-md-6 bg-slate-50 p-5 d-flex align-items-center justify-content-center relative group">
                <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $product['image'] ?? 'default.jpg' }}" 
                     class="img-fluid max-h-[450px] transition-transform duration-500 group-hover:scale-105" 
                     alt="{{ $product['name'] }}"
                     onerror="this.src='https://placehold.co/600x600?text=TechMart+Image'">
                
                <div class="absolute top-4 left-4">
                    <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-patch-check-fill me-1"></i> CHÍNH HÃNG 100%
                    </span>
                </div>
            </div>

            <div class="col-md-6 p-5 d-flex flex-column">
                <div class="mb-auto">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="text-blue-600 fw-bold text-xs text-uppercase tracking-widest">
                            {{ $product['category_name'] ?? 'Công nghệ' }}
                        </span>
                        <span class="text-slate-300">|</span>
                        <div class="text-warning small">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-half"></i>
                            <span class="text-muted ms-1">(4.8/5)</span>
                        </div>
                    </div>

                    <h1 class="display-6 fw-black text-slate-900 mb-3 tracking-tighter uppercase">
                        {{ $product['name'] }}
                    </h1>
                    
                    <div class="d-flex align-items-baseline gap-3 mb-4">
                        <h2 class="text-rose-600 fw-black display-6 mb-0">
                            {{ number_format($product['price'], 0, ',', '.') }}đ
                        </h2>
                        <span class="text-muted text-decoration-line-through small">
                            {{ number_format($product['price'] * 1.2, 0, ',', '.') }}đ
                        </span>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-4 p-4 mb-4">
                        <h6 class="fw-bold text-blue-800 mb-2 small uppercase"><i class="bi bi-gift-fill me-2"></i>Chính sách TechMart</h6>
                        <ul class="list-unstyled mb-0 small text-blue-700 space-y-1">
                            <li><i class="bi bi-check2-circle me-2"></i>Bảo hành 12 tháng chính hãng toàn quốc</li>
                            <li><i class="bi bi-check2-circle me-2"></i>Lỗi 1 đổi 1 trong 30 ngày đầu tiên</li>
                            <li><i class="bi bi-check2-circle me-2"></i>Miễn phí vận chuyển cho đơn hàng từ 10tr</li>
                        </ul>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2 small uppercase tracking-wider">Mô tả ngắn:</h6>
                        <p class="text-muted leading-relaxed small">
                            {{ $product['description'] ?? 'Sản phẩm ' . $product['name'] . ' là dòng thiết bị công nghệ cao cấp nhất hiện nay, được phân phối chính hãng tại TechMart. Với thiết kế tinh tế và hiệu năng vượt trội, đây là lựa chọn hàng đầu cho các tín đồ công nghệ.' }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <div class="row g-3">
                        <div class="col-8">
                            <a href="{{ rtrim(BASE_URL, '/') }}/cart/add/{{ $product['id'] }}" 
                               class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-lg shadow-blue-200 transition-all hover:-translate-y-1 text-white">
                                <i class="bi bi-cart-plus-fill me-2"></i> THÊM VÀO GIỎ HÀNG
                            </a>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-secondary btn-lg w-100 rounded-pill py-3 shadow-sm transition-all hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-4 d-flex justify-content-between text-center">
                        <div class="small">
                            <i class="bi bi-truck fs-4 text-primary d-block mb-1"></i>
                            <span class="text-muted extra-small">Giao hàng 2h</span>
                        </div>
                        <div class="small border-start border-end px-4">
                            <i class="bi bi-shield-check fs-4 text-primary d-block mb-1"></i>
                            <span class="text-muted extra-small">Chính hãng</span>
                        </div>
                        <div class="small">
                            <i class="bi bi-arrow-repeat fs-4 text-primary d-block mb-1"></i>
                            <span class="text-muted extra-small">Đổi trả 7 ngày</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card border-0 shadow-sm rounded-5 overflow-hidden">
        <div class="card-body p-5">
            <ul class="nav nav-tabs border-0 gap-4 mb-4" id="productTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active border-0 fw-bold px-0 text-dark position-relative after-line" data-bs-toggle="tab" data-bs-target="#desc" type="button">MÔ TẢ CHI TIẾT SẢN PHẨM</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link border-0 fw-bold px-0 text-muted" data-bs-toggle="tab" data-bs-target="#spec" type="button">THÔNG SỐ KỸ THUẬT</button>
                </li>
            </ul>
            <div class="tab-content pt-2" id="productTabContent">
                <div class="tab-pane fade show active" id="desc">
                    <p class="text-muted leading-loose">
                        Nội dung chi tiết về sản phẩm {{ $product['name'] }} đang được chúng tôi cập nhật sớm nhất. 
                        Sản phẩm đảm bảo các tiêu chuẩn chất lượng cao nhất từ nhà sản xuất. 
                        Hãy liên hệ hotline để được tư vấn cụ thể hơn về các tính năng đặc biệt của dòng sản phẩm này.
                    </p>
                </div>
                <div class="tab-pane fade" id="spec">
                    <table class="table table-striped rounded-4 overflow-hidden border border-slate-100">
                        <tbody class="text-sm">
                            <tr><th width="30%" class="ps-4 py-3">Thương hiệu</th><td class="text-muted">{{ $product['brand_name'] ?? 'Chính hãng' }}</td></tr>
                            <tr><th class="ps-4 py-3">Phân loại</th><td class="text-muted">{{ $product['category_name'] ?? 'Thiết bị' }}</td></tr>
                            <tr><th class="ps-4 py-3">Tình trạng</th><td class="text-muted">Mới 100% nguyên Seal</td></tr>
                            <tr><th class="ps-4 py-3">Phụ kiện</th><td class="text-muted">Hộp, Sách hướng dẫn, Cáp sạc</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 10px; }
    .nav-tabs .nav-link.active { background: transparent !important; color: #2563eb !important; }
    .after-line.active::after {
        content: ''; position: absolute; bottom: -5px; left: 0; width: 100%; height: 3px;
        background: #2563eb; border-radius: 10px;
    }
    
    @keyframes bounceIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-bounce-in { animation: bounceIn 0.5s ease-out; }
</style>

@include('user.layouts.footer')