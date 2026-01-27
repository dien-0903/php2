@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
@endphp

@include('user.layouts.header')

<div class="mb-5">
    <div class="text-center py-5 bg-white rounded-5 shadow-sm border border-slate-100 mb-5">
        <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-2">
            <i class="bi bi-ticket-perforated text-primary me-2"></i>Kho Voucher TechMart
        </h1>
        <p class="text-slate-500 max-w-lg mx-auto">Sưu tầm ngay những mã giảm giá độc quyền để nhận ưu đãi cực hời khi mua sắm các thiết bị công nghệ đỉnh cao.</p>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-8 text-dark">
            <div class="input-group bg-white rounded-pill shadow-sm p-2 border border-slate-200">
                <span class="input-group-text bg-transparent border-0 px-3 text-slate-400">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="ajax-search-input" value="{{ $search ?? '' }}" 
                       class="form-control border-0 shadow-none px-2 py-2" 
                       placeholder="Nhập mã giảm giá và xem kết quả ngay...">
                <div id="search-loader" class="d-none spinner-border spinner-border-sm text-primary my-auto me-3"></div>
            </div>
            <div class="text-center mt-2 small text-slate-400 font-medium italic">Kết quả tự động cập nhật không cần tải lại trang</div>
        </div>
    </div>

    <div id="coupon-list-container" class="row g-4 text-dark">
        @if(empty($coupons))
            <div class="text-center py-10 w-100">
                <h4 class="text-slate-400 fw-bold">Hiện không có mã giảm giá nào khả dụng!</h4>
            </div>
        @else
            @foreach($coupons as $coupon)
                <div class="col-md-6 col-lg-4">
                    <div class="bg-white rounded-4 shadow-sm border border-slate-100 overflow-hidden position-relative h-100 transition-all hover:-translate-y-1 hover:shadow-lg">
                        <div class="position-absolute top-50 start-0 translate-middle-y bg-slate-50 rounded-circle" style="width: 20px; height: 20px; margin-left: -10px; border: 1px solid #f1f5f9; z-index: 2;"></div>
                        <div class="position-absolute top-50 end-0 translate-middle-y bg-slate-50 rounded-circle" style="width: 20px; height: 20px; margin-right: -10px; border: 1px solid #f1f5f9; z-index: 2;"></div>

                        <div class="d-flex h-100">
                            <div class="bg-primary p-4 d-flex flex-column align-items-center justify-content-center text-white text-center" style="width: 110px; border-right: 2px dashed rgba(255,255,255,0.3);">
                                <span class="small fw-bold opacity-75 uppercase">GIẢM</span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fs-2 fw-black">
                                        {{ $coupon['type'] == 'percent' ? $coupon['value'] : number_format($coupon['value']/1000) }}
                                    </span>
                                    <span class="fw-bold ms-1">{{ $coupon['type'] == 'percent' ? '%' : 'K' }}</span>
                                </div>
                            </div>
                            <div class="p-4 flex-grow-1 d-flex flex-column">
                                <div class="mb-auto">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h4 class="font-mono fw-black text-dark mb-0 tracking-tighter fs-4 uppercase">{{ $coupon['code'] }}</h4>
                                        <span class="badge bg-blue-50 text-blue-600 border rounded-pill px-2 py-1" style="font-size: 10px;">{{ $coupon['type'] == 'percent' ? 'Mã %' : 'Tiền mặt' }}</span>
                                    </div>
                                    <p class="text-slate-500 small mb-0 mt-1 line-clamp-2">Áp dụng cho mọi đơn hàng tại TechMart.</p>
                                </div>
                                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <div class="small text-slate-400 font-bold uppercase" style="font-size: 9px;">HSD: VÔ HẠN</div>
                                    <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold btn-copy-code" data-code="{{ $coupon['code'] }}">SAO CHÉP</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div id="pagination-container" class="mt-10 d-flex justify-content-center gap-2">
        @if(isset($totalPages) && $totalPages > 1)
            @for($i = 1; $i <= $totalPages; $i++)
                <button class="btn {{ $currentPage == $i ? 'btn-primary text-white' : 'btn-white border-slate-200 text-dark' }} rounded-3 shadow-sm px-3 fw-bold page-btn-ajax" data-page="{{ $i }}">
                    {{ $i }}
                </button>
            @endfor
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ajax-search-input');
    const listContainer = document.getElementById('coupon-list-container');
    const paginationContainer = document.getElementById('pagination-container');
    const loader = document.getElementById('search-loader');

    function fetchCoupons(query = '', page = 1) {
        loader.classList.remove('d-none');
        const currentPath = window.location.pathname;
        const url = currentPath + "?ajax=1&search=" + encodeURIComponent(query) + "&page=" + page;

        fetch(url)
            .then(res => res.json())
            .then(res => {
                renderList(res.data);
                renderPagination(res.totalPages, page);
                loader.classList.add('d-none');
            })
            .catch(err => {
                console.error('Lỗi nhận dữ liệu:', err);
                loader.classList.add('d-none');
            });
    }

    function renderList(coupons) {
        if (!coupons || coupons.length === 0) {
            listContainer.innerHTML = '<div class="text-center py-10 w-100 text-slate-400 font-bold">Không tìm thấy mã nào phù hợp!</div>';
            return;
        }
        listContainer.innerHTML = coupons.map(c => {
            const val = c.type === 'percent' ? c.value : (c.value / 1000);
            const unit = c.type === 'percent' ? '%' : 'K';
            const badge = c.type === 'percent' ? 'Mã %' : 'Tiền mặt';
            return `
                <div class="col-md-6 col-lg-4 animate-fade-in">
                    <div class="bg-white rounded-4 shadow-sm border border-slate-100 overflow-hidden position-relative h-100 transition-all hover:-translate-y-1 hover:shadow-lg">
                        <div class="position-absolute top-50 start-0 translate-middle-y bg-slate-50 rounded-circle" style="width: 20px; height: 20px; margin-left: -10px; border: 1px solid #f1f5f9; z-index: 2;"></div>
                        <div class="position-absolute top-50 end-0 translate-middle-y bg-slate-50 rounded-circle" style="width: 20px; height: 20px; margin-right: -10px; border: 1px solid #f1f5f9; z-index: 2;"></div>
                        <div class="d-flex h-100">
                            <div class="bg-primary p-4 d-flex flex-column align-items-center justify-content-center text-white text-center" style="width: 110px; border-right: 2px dashed rgba(255,255,255,0.3);">
                                <span class="small fw-bold opacity-75 uppercase">GIẢM</span>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="fs-2 fw-black">${val}</span>
                                    <span class="fw-bold ms-1">${unit}</span>
                                </div>
                            </div>
                            <div class="p-4 flex-grow-1 d-flex flex-column">
                                <div class="mb-auto text-dark">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h4 class="font-mono fw-black text-dark mb-0 tracking-tighter fs-4 uppercase">${c.code}</h4>
                                        <span class="badge bg-blue-50 text-blue-600 border rounded-pill px-2 py-1" style="font-size: 10px;">${badge}</span>
                                    </div>
                                    <p class="text-slate-500 small mb-0 mt-1">Áp dụng cho mọi đơn hàng tại TechMart.</p>
                                </div>
                                <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                    <div class="small text-slate-400 font-bold uppercase" style="font-size: 9px;">HSD: VÔ HẠN</div>
                                    <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold btn-copy-code" data-code="${c.code}">SAO CHÉP</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
        }).join('');
    }

    function renderPagination(total, current) {
        if (total <= 1) { paginationContainer.innerHTML = ''; return; }
        let html = '';
        for (let i = 1; i <= total; i++) {
            const cls = (i == current) ? 'btn-primary text-white' : 'btn-white border-slate-200 text-dark';
            html += '<button class="btn ' + cls + ' rounded-3 shadow-sm px-3 fw-bold page-btn-ajax" data-page="' + i + '">' + i + '</button>';
        }
        paginationContainer.innerHTML = html;
    }

    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => fetchCoupons(this.value, 1), 400);
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('page-btn-ajax')) {
            fetchCoupons(searchInput.value, e.target.dataset.page);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
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

<style>
.animate-fade-in { animation: fadeIn 0.4s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

@include('user.layouts.footer')