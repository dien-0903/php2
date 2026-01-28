@include('user.layouts.header')

@if(isset($_SESSION['success']))
    <div class="container mt-4">
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 animate-bounce-in" role="alert">
            <div class="d-flex align-items-center text-success fw-bold">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <span>{{ $_SESSION['success'] }}</span>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    @php unset($_SESSION['success']); @endphp
@endif

<div class="container py-4">

    <div class="mb-5">
        <div class="card border-0 rounded-4 overflow-hidden shadow-lg relative group" style="height: 400px;">
            <img src="https://placehold.co/1200x500/1e293b/FFF?text=New+Collection" class="w-100 h-100 object-cover transition-transform duration-700 group-hover:scale-105" alt="Banner">
            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center px-4 px-md-5" style="background: linear-gradient(to right, rgba(0,0,0,0.7), transparent);">
                <div class="text-white ps-md-4">
                    <span class="badge bg-danger rounded-pill mb-3 px-3 py-2 animate-bounce-in">NEW ARRIVAL</span>
                    <h1 class="fw-bold display-4 mb-2 tracking-tighter">CÔNG NGHỆ MỚI</h1>
                    <p class="text-light mb-4 fs-5">Khám phá những thiết bị vừa cập bến TechStore.</p>
                    <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary shadow">
                        MUA NGAY <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="fw-black text-uppercase mb-1" style="color: #0f172a; font-weight: 900; letter-spacing: -1px;">
                    MÃ GIẢM GIÁ HOT
                </h2>
                <p class="text-secondary mb-0">Săn deal hời, chốt đơn ngay hôm nay</p>
            </div>
            <a href="#" class="btn btn-outline-dark rounded-pill px-4 fw-bold d-flex align-items-center gap-2" style="width: fit-content;">
                Xem kho Voucher <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="row g-3">
            @if(empty($coupons))
                <div class="col-12">
                    <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                        <p class="text-secondary fw-bold mb-0">Hiện chưa có mã giảm giá nào.</p>
                    </div>
                </div>
            @else
                @foreach($coupons as $coupon)
                    <div class="col-md-6 col-lg-4">
                        <div class="bg-white rounded-4 shadow-sm border border-slate-100 overflow-hidden position-relative h-100 transition-all hover-translate-up">
                            <div class="position-absolute top-50 start-0 translate-middle-y bg-body rounded-circle" style="width: 20px; height: 20px; margin-left: -10px; border: 1px solid #f1f5f9; z-index: 2;"></div>
                            <div class="position-absolute top-50 end-0 translate-middle-y bg-body rounded-circle" style="width: 20px; height: 20px; margin-right: -10px; border: 1px solid #f1f5f9; z-index: 2;"></div>

                            <div class="d-flex h-100">
                                <div class="bg-primary p-3 d-flex flex-column align-items-center justify-content-center text-white text-center" style="width: 100px; min-width: 100px; border-right: 2px dashed rgba(255,255,255,0.3);">
                                    <span class="small fw-bold opacity-75" style="font-size: 10px;">GIẢM</span>
                                    <div class="d-flex align-items-baseline mt-1">
                                        <span class="h4 fw-black mb-0">
                                            {{ $coupon['type'] == 'percent' ? $coupon['value'] : number_format($coupon['value']/1000) }}
                                        </span>
                                        <span class="fw-bold ms-1" style="font-size: 12px;">{{ $coupon['type'] == 'percent' ? '%' : 'K' }}</span>
                                    </div>
                                </div>
                                <div class="p-3 flex-grow-1 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="font-mono fw-black text-dark mb-0 tracking-tighter">{{ $coupon['code'] }}</h6>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill" style="font-size: 9px;">
                                                {{ $coupon['type'] == 'percent' ? 'Voucher %' : 'Tiền mặt' }}
                                            </span>
                                        </div>
                                        <p class="text-secondary small mb-0 line-clamp-1" style="font-size: 12px;">Áp dụng toàn sàn MD</p>
                                    </div>
                                    <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                                        <div class="text-secondary fw-bold" style="font-size: 10px;">
                                            HSD: {{ isset($coupon['end_date']) ? date('d/m/Y', strtotime($coupon['end_date'])) : 'VÔ HẠN' }}
                                        </div>
                                        <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold btn-copy-code" data-code="{{ $coupon['code'] }}" style="font-size: 10px; padding-top: 4px; padding-bottom: 4px;">
                                            LẤY MÃ
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-black text-uppercase mb-1" style="color: #0f172a; font-weight: 900; letter-spacing: -1px;">
                SẢN PHẨM MỚI
            </h2>
            <p class="text-secondary mb-0">Những siêu phẩm công nghệ vừa ra mắt</p>
        </div>
        
        <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-outline-primary rounded-pill px-4 fw-bold d-flex align-items-center gap-2" style="width: fit-content;">
            Xem tất cả <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="row g-4 mb-5">
        @if(isset($newProducts) && !empty($newProducts))
            @foreach($newProducts as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden group bg-white hover-shadow transition-all">
                        <a href="{{ rtrim(BASE_URL, '/') }}/product/show/{{ $item['id'] }}" class="text-decoration-none">
                            <div class="bg-light p-4 d-flex align-items-center justify-content-center position-relative overflow-hidden" style="height: 220px;">
                                <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $item['image'] ?? 'default.jpg' }}" 
                                     class="mw-100 mh-100 object-contain transition-transform duration-500" 
                                     style="max-height: 150px;" 
                                     alt="{{ $item['name'] }}"
                                     onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary rounded-pill px-2 py-1 small fw-bold shadow-sm" style="font-size: 10px;">NEW</span>
                                </div>
                            </div>
                            <div class="card-body p-3 d-flex flex-column text-dark">
                                <div class="mb-auto">
                                    <span class="text-primary fw-bold text-uppercase" style="font-size: 10px; letter-spacing: 1px;">
                                        {{ $item['category_name'] ?? 'Thiết bị' }}
                                    </span>
                                    <h6 class="card-title fw-bold text-dark mt-1 mb-2 text-truncate-2" style="min-height: 2.5rem;">
                                        {{ $item['name'] }}
                                    </h6>
                                </div>
                                <div class="mt-2">
                                    <p class="text-danger fw-bold fs-5 mb-0">
                                        {{ number_format($item['price'], 0, ',', '.') }}đ
                                    </p>
                                </div>
                            </div>
                        </a>
                        <div class="card-body pt-0 px-3 pb-3">
                            <div class="d-grid">
                                <a href="{{ rtrim(BASE_URL, '/') }}/cart/add/{{ $item['id'] }}" 
                                   class="btn btn-primary rounded-pill fw-bold py-2 shadow-sm text-white" style="font-size: 11px;">
                                    <i class="bi bi-cart-plus me-1"></i> THÊM VÀO GIỎ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12 text-center py-5">
                <div class="bg-white p-5 rounded-4 shadow-sm border border-dashed">
                    <i class="bi bi-box-seam fs-1 text-secondary mb-3 d-block"></i>
                    <p class="text-secondary fw-bold">Đang cập nhật sản phẩm mới...</p>
                </div>
            </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-copy-code')) {
            const btn = e.target;
            navigator.clipboard.writeText(btn.dataset.code).then(() => {
                const old = btn.innerText;
                btn.innerText = 'ĐÃ LƯU';
                btn.classList.replace('btn-dark', 'btn-success');
                setTimeout(() => {
                    btn.innerText = old;
                    btn.classList.replace('btn-success', 'btn-dark');
                }, 2000);
            });
        }
    });
});
</script>


@include('user.layouts.footer')