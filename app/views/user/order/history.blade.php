@include('user.layouts.header')

<div class="container py-5 text-dark mb-5 animate-fade-in text-start">
    <!-- Tiêu đề trang -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-black mb-0 uppercase tracking-tighter text-primary">
                <i class="bi bi-bag-check-fill me-2"></i>Lịch sử mua hàng
            </h2>
            <p class="text-muted small mb-0 mt-1">Quản lý và theo dõi hành trình tất cả đơn hàng của bạn.</p>
        </div>
        <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift">
            <i class="bi bi-cart-plus me-1"></i> TIẾP TỤC MUA SẮM
        </a>
    </div>

    <!-- THANH TÌM KIẾM & LỌC (THIẾT KẾ CHUẨN THEO ẢNH) -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 bg-white border border-slate-100 overflow-hidden">
        <div class="card-body p-4 p-md-5">
            <form action="{{ rtrim(BASE_URL, '/') }}/order/history" method="GET" class="row g-4 align-items-end">
                <!-- Bộ chọn Trạng thái -->
                <div class="col-md-3">
                    <label class="form-label extra-small fw-bold text-muted uppercase tracking-wider mb-2 ms-2">TRẠNG THÁI</label>
                    <div class="rounded-pill border px-2 bg-light">
                        <select name="status" class="form-select border-0 shadow-none py-2.5 fw-bold text-secondary bg-transparent cursor-pointer">
                            <option value="">Tất cả trạng thái</option>
                            <option value="0" {{ (isset($status) && $status === '0') ? 'selected' : '' }}>🕒 Chờ xử lý</option>
                            <option value="1" {{ (isset($status) && $status === '1') ? 'selected' : '' }}>✅ Đã xác nhận</option>
                            <option value="2" {{ (isset($status) && $status === '2') ? 'selected' : '' }}>🚚 Đang giao hàng</option>
                            <option value="3" {{ (isset($status) && $status === '3') ? 'selected' : '' }}>🎉 Hoàn thành</option>
                            <option value="4" {{ (isset($status) && $status === '4') ? 'selected' : '' }}>❌ Đã hủy</option>
                        </select>
                    </div>
                </div>

                <!-- Ô Tìm kiếm -->
                <div class="col-md-5">
                    <label class="form-label extra-small fw-bold text-muted uppercase tracking-wider mb-2 ms-2">TÌM KIẾM</label>
                    <div class="input-group rounded-pill border overflow-hidden bg-light">
                        <span class="input-group-text bg-transparent border-0 ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none py-2.5 ps-2" 
                               placeholder="Mã đơn hàng" value="{{ $search ?? '' }}">
                    </div>
                </div>

                <!-- Các Nút Hành Động -->
                <div class="col-md-4">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2.5 shadow-sm transition-all hover-lift uppercase">
                            LỌC DỮ LIỆU
                        </button>
                        <a href="{{ rtrim(BASE_URL, '/') }}/order/history" class="btn btn-outline-secondary rounded-pill py-2.5 px-4 shadow-sm transition-all hover-lift uppercase text-nowrap">
                            XÓA LỌC
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- THÔNG BÁO HỆ THỐNG -->
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center p-3 animate-slide-down">
            <div class="bg-success text-white rounded-circle p-1 me-3 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="fw-bold">{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    <!-- DANH SÁCH ĐƠN HÀNG -->
    <div class="row g-4">
        <div class="col-lg-12">
            @forelse($orders as $o)
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden bg-white hover-shadow-lg transition-all border border-slate-100">
                <!-- Header Đơn hàng -->
                <div class="card-header bg-white border-bottom p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="badge bg-dark text-white rounded-pill px-3 py-2 extra-small fw-bold">#{{ $o['order_code'] }}</span>
                        <span class="text-muted mx-1 d-none d-md-inline">|</span>
                        <span class="small text-secondary fw-medium">
                            <i class="bi bi-calendar3 me-1"></i>{{ date('d/m/Y', strtotime($o['created_at'])) }}
                            <span class="ms-1 opacity-50">{{ date('H:i', strtotime($o['created_at'])) }}</span>
                        </span>
                    </div>
                    <div>
                        @php
                            $statusMap = [
                                0 => ['bg-warning-subtle text-warning', 'Chờ xử lý', 'bi-clock-history'],
                                1 => ['bg-info-subtle text-info', 'Đã xác nhận', 'bi-clipboard-check'],
                                2 => ['bg-primary-subtle text-primary', 'Đang giao hàng', 'bi-truck'],
                                3 => ['bg-success-subtle text-success', 'Hoàn thành', 'bi-check-circle-fill'],
                                4 => ['bg-danger-subtle text-danger', 'Đã hủy', 'bi-x-circle']
                            ];
                            $st = $statusMap[$o['status']] ?? ['bg-secondary-subtle text-secondary', 'Không rõ', 'bi-question-circle'];
                        @endphp
                        <span class="badge {{ $st[0] }} border-0 px-3 py-2 rounded-pill fw-bold uppercase" style="font-size: 10px;">
                            <i class="bi {{ $st[2] }} me-1"></i> {{ $st[1] }}
                        </span>
                    </div>
                </div>

                <!-- Body Đơn hàng -->
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-start mb-2">
                                <div class="bg-light rounded-3 p-2 me-3 flex-shrink-0 border">
                                    <i class="bi bi-person-vcard text-primary fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $o['recipient_name'] }}</div>
                                    <div class="small text-muted fw-medium mb-1"><i class="bi bi-phone me-1"></i>{{ $o['phone'] }}</div>
                                    <div class="extra-small text-secondary lh-sm">{{ $o['address'] }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 text-md-end border-start-md">
                            <div class="mb-1">
                                <span class="extra-small text-muted fw-bold uppercase tracking-wider">Tổng số tiền thanh toán</span>
                            </div>
                            <div class="h3 fw-black text-danger mb-0 tracking-tighter">{{ number_format($o['total_amount']) }}đ</div>
                            
                            <div class="d-flex flex-column align-items-md-end gap-1">
                                <span class="badge bg-light text-dark border extra-small fw-bold px-2 py-1">
                                    {{ $o['payment_method'] == 'cod' ? '💵 Thanh toán tiền mặt' : '💳 Chuyển khoản Online' }}
                                </span>
                                <span class="extra-small text-success fw-bold italic mt-1">
                                    <i class="bi bi-shield-check me-1"></i> Giá đã bao gồm ưu đãi
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Đơn hàng -->
                <div class="card-footer bg-slate-50 border-0 p-3 text-end d-flex flex-wrap justify-content-end gap-2">
                    <div class="me-auto d-none d-md-block pt-1">
                        <span class="extra-small text-muted italic">Cần hỗ trợ? <a href="#" class="text-primary text-decoration-none fw-bold">MD Support</a></span>
                    </div>

                    <a href="{{ rtrim(BASE_URL, '/') }}/order/detail/{{ $o['id'] }}" class="btn btn-white border shadow-sm btn-sm rounded-pill px-4 fw-bold transition-all hover-lift">
                        <i class="bi bi-eye me-1 text-primary"></i> CHI TIẾT
                    </a>
                    
                    @if($o['status'] == 0)
                        <a href="{{ rtrim(BASE_URL, '/') }}/order/cancel/{{ $o['id'] }}" 
                           class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold transition-all hover-lift" 
                           onclick="return confirm('Xác nhận yêu cầu hủy đơn hàng này?')">
                           <i class="bi bi-trash3 me-1"></i> HỦY ĐƠN
                        </a>
                    @endif

                    <a href="{{ rtrim(BASE_URL, '/') }}/order/reorder/{{ $o['id'] }}" 
                       class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift">
                       <i class="bi bi-arrow-repeat me-1"></i> MUA LẠI
                    </a>
                </div>
            </div>
            @empty
            <div class="text-center py-5 bg-white rounded-5 shadow-sm border border-dashed border-2">
                <i class="bi bi-search display-1 text-muted opacity-25"></i>
                <h4 class="fw-bold text-dark mt-3">Không tìm thấy đơn hàng nào</h4>
                <p class="text-muted">Vui lòng thay đổi từ khóa tìm kiếm hoặc trạng thái lọc.</p>
                <a href="{{ rtrim(BASE_URL, '/') }}/order/history" class="btn btn-dark rounded-pill px-5 py-2.5 fw-bold mt-2 uppercase">Làm mới bộ lọc</a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- PHÂN TRANG (PAGINATION) -->
    @if(isset($totalPages) && $totalPages > 1)
    <nav class="mt-5">
        <ul class="pagination justify-content-center gap-2">
            <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white" 
                   href="?page={{ $currentPage - 1 }}&status={{ $status }}&search={{ urlencode($search) }}">
                   <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            @for($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ $currentPage == $i ? 'active' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold {{ $currentPage == $i ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark' }}" 
                       href="?page={{ $i }}&status={{ $status }}&search={{ urlencode($search) }}">
                       {{ $i }}
                    </a>
                </li>
            @endfor

            <li class="page-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
                <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white" 
                   href="?page={{ $currentPage + 1 }}&status={{ $status }}&search={{ urlencode($search) }}">
                   <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    @endif
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 10px; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .tracking-tighter { letter-spacing: -1.5px; }
    .bg-slate-50 { background-color: #f8fafc; }
    .hover-shadow-lg:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .transition-all { transition: all 0.3s ease; }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    /* Màu trạng thái */
    .bg-warning-subtle { background-color: #fff9db !important; color: #f59f00 !important; }
    .bg-info-subtle { background-color: #e7f5ff !important; color: #1c7ed6 !important; }
    .bg-primary-subtle { background-color: #e7f5ff !important; color: #339af0 !important; }
    .bg-success-subtle { background-color: #ebfbee !important; color: #37b24d !important; }
    .bg-danger-subtle { background-color: #fff5f5 !important; color: #f03e3e !important; }

    @media (min-width: 768px) {
        .border-start-md { border-left: 1px solid #f1f5f9 !important; }
    }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
</style>

@include('user.layouts.footer')