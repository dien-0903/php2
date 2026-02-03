@php
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    // 1. TÍNH TOÁN LẠI TỔNG TIỀN HÀNG GỐC (Chưa giảm) từ danh sách sản phẩm
    $actualSubtotal = 0;
    foreach($items as $item) {
        $actualSubtotal += $item['price'] * $item['quantity'];
    }
    
    // 2. TÍNH SỐ TIỀN ĐÃ ĐƯỢC GIẢM (Tiền hàng gốc - Tiền thực tế đã thanh toán)
    // Nếu kết quả > 0 tức là đơn hàng có dùng MGG
    $orderDiscount = $actualSubtotal - $order['total_amount'];
    if($orderDiscount < 0) $orderDiscount = 0;

    // 3. Cấu hình các bước trạng thái (Timeline)
    $statusSteps = [
        0 => ['label' => 'Đã đặt hàng', 'icon' => 'bi-cart-check'],
        1 => ['label' => 'Đã xác nhận', 'icon' => 'bi-clipboard-check'],
        2 => ['label' => 'Đang giao hàng', 'icon' => 'bi-truck'],
        3 => ['label' => 'Hoàn thành', 'icon' => 'bi-house-check']
    ];
    $currentStatus = $order['status'];
@endphp

@include('user.layouts.header')

<div class="container py-5 text-dark mb-5 animate-fade-in text-start">
    <!-- Header Điều hướng -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb small mb-0">
                    <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/order/history" class="text-decoration-none text-muted">Lịch sử đơn hàng</a></li>
                    <li class="breadcrumb-item active fw-bold" aria-current="page">Chi tiết đơn #{{ $order['order_code'] }}</li>
                </ol>
            </nav>
            <h2 class="fw-black mb-0 uppercase tracking-tighter">
                <i class="bi bi-receipt-cutoff text-primary me-2"></i>Chi tiết đơn hàng
            </h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ rtrim(BASE_URL, '/') }}/order/history" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift">
                <i class="bi bi-arrow-left me-1"></i> TRỞ LẠI
            </a>
            @if($currentStatus == 3)
                <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> IN HÓA ĐƠN
                </button>
            @endif
        </div>
    </div>

    <!-- Thanh trạng thái đơn hàng (Timeline Tracker) -->
    @if($currentStatus != 4)
    <div class="card border-0 shadow-sm rounded-5 bg-white mb-5 overflow-hidden border border-slate-100">
        <div class="card-body p-4 p-md-5">
            <div class="order-tracker d-flex justify-content-between position-relative">
                @foreach($statusSteps as $val => $step)
                    <div class="tracker-step text-center position-relative z-1 {{ $currentStatus >= $val ? 'active' : '' }}">
                        <div class="step-icon-wrapper rounded-circle mx-auto mb-3 shadow-sm d-flex align-items-center justify-content-center">
                            <i class="bi {{ $step['icon'] }} fs-4"></i>
                        </div>
                        <div class="step-label fw-bold small uppercase">{{ $step['label'] }}</div>
                        @if($currentStatus == $val)
                            <div class="extra-small text-muted mt-1 italic">{{ date('d/m/Y H:i', strtotime($order['updated_at'] ?? $order['created_at'])) }}</div>
                        @endif
                    </div>
                @endforeach
                <!-- Đường kẻ nối -->
                <div class="tracker-line position-absolute top-50 start-0 translate-middle-y w-100 bg-light" style="height: 6px; z-index: 0; margin-top: -15px; border-radius: 10px;">
                    <div class="line-progress h-100 bg-primary transition-all shadow-sm" style="width: {{ ($currentStatus / 3) * 100 }}%; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-5 p-4 d-flex align-items-center animate-shake">
        <div class="bg-danger text-white rounded-circle p-2 me-4 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-x-circle-fill fs-3"></i>
        </div>
        <div>
            <h5 class="fw-black mb-1 uppercase">ĐƠN HÀNG ĐÃ BỊ HỦY</h5>
            <p class="mb-0 small opacity-75">Yêu cầu hủy được thực hiện vào lúc {{ date('H:i d/m/Y', strtotime($order['updated_at'])) }}.</p>
        </div>
    </div>
    @endif

    <div class="row g-4">
        <!-- Cột trái: Sản phẩm & Ghi chú -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white border border-slate-100">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold uppercase"><i class="bi bi-box-seam me-2 text-primary"></i>Danh mục sản phẩm ({{ count($items) }})</h6>
                    <span class="badge bg-light text-muted border px-3 rounded-pill extra-small fw-bold">Mã đơn: #{{ $order['order_code'] }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-slate-50">
                            <tr class="text-muted extra-small fw-bold uppercase">
                                <th class="ps-4 py-3 border-0">Sản phẩm</th>
                                <th class="py-3 border-0 text-center">Đơn giá</th>
                                <th class="py-3 border-0 text-center">Số lượng</th>
                                <th class="pe-4 py-3 border-0 text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($items as $item)
                            <tr>
                                <td class="ps-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-light rounded-4 p-1 border me-3 flex-shrink-0 shadow-inner" style="width: 75px; height: 75px;">
                                            @php $imgUrl = 'http://localhost/PHP2/public/uploads/products/' . ($item['product_image'] ?: 'default.jpg'); @endphp
                                            <img src="{{ $imgUrl }}" class="w-100 h-100 object-fit-contain rounded-3" onerror="this.src='https://placehold.co/200x200?text=SP'">
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark fs-6 mb-1">{{ $item['product_name'] }}</div>
                                            <span class="badge bg-primary-subtle text-primary border-0 rounded-pill extra-small px-2 fw-bold uppercase">
                                                {{ $item['variant_info'] ?: 'Phiên bản chuẩn' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-medium text-dark">{{ number_format($item['price']) }}đ</td>
                                <td class="text-center fw-bold text-slate-400">x{{ $item['quantity'] }}</td>
                                <td class="pe-4 text-end fw-black text-danger fs-5">{{ number_format($item['price'] * $item['quantity']) }}đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Tổng kết tài chính (SỬA LỖI GIÁ TIỀN TẠI ĐÂY) -->
                <div class="card-footer bg-slate-50 p-4 border-0">
                    <div class="row justify-content-end">
                        <div class="col-md-7 col-lg-7">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-bold small uppercase">Tạm tính (Giá gốc):</span>
                                <span class="fw-bold text-dark">{{ number_format($actualSubtotal) }}đ</span>
                            </div>
                            
                            {{-- LOGIC: CHỈ HIỆN KHI CÓ GIẢM GIÁ THẬT SỰ --}}
                            @if($orderDiscount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success animate-bounce-in">
                                <span class="fw-bold small uppercase"><i class="bi bi-ticket-perforated-fill me-1"></i> Ưu đãi Voucher MD:</span>
                                <span class="fw-black">-{{ number_format($orderDiscount) }}đ</span>
                            </div>
                            @endif

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-bold small uppercase">Phí vận chuyển:</span>
                                <span class="text-success fw-bold small uppercase">MIỄN PHÍ</span>
                            </div>
                            
                            <hr class="my-3 opacity-10">
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 fw-black mb-0 uppercase tracking-tighter text-dark">TỔNG THANH TOÁN:</span>
                                <span class="h2 fw-black text-primary mb-0 tracking-tighter">{{ number_format($order['total_amount']) }}đ</span>
                            </div>
                            
                            @if($orderDiscount > 0)
                                <div class="text-end mt-2">
                                    <span class="badge bg-success text-white border-0 px-3 py-1 rounded-pill extra-small fw-bold shadow-sm">
                                        TIẾT KIỆM ĐƯỢC {{ number_format($orderDiscount) }}Đ
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="card border-0 shadow-sm rounded-4 bg-white border border-slate-100">
                <div class="card-body p-4">
                    <h6 class="fw-black mb-3 uppercase text-muted small tracking-widest border-bottom pb-2">
                        <i class="bi bi-chat-dots me-2 text-primary"></i>Ghi chú từ khách hàng
                    </h6>
                    <div class="p-3 bg-light rounded-3 text-secondary italic small border">
                        {{ $order['note'] ?: 'Không có yêu cầu đặc biệt cho đơn hàng này.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: Thông tin nhận hàng & Thao tác -->
        <div class="col-lg-4">
            <!-- Thông tin vận chuyển -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 border border-slate-100">
                <h6 class="fw-black mb-4 uppercase text-muted small tracking-widest border-bottom pb-2">Thông tin nhận hàng</h6>
                
                <div class="mb-4 d-flex align-items-center">
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-5">{{ $order['recipient_name'] }}</div>
                        <div class="extra-small text-muted fw-bold uppercase">Khách hàng MD</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="extra-small text-muted d-block uppercase fw-bold mb-1">Số điện thoại:</label>
                    <div class="fw-bold text-dark fs-6"><i class="bi bi-telephone text-primary me-2"></i>{{ $order['phone'] }}</div>
                </div>

                <div class="mb-4">
                    <label class="extra-small text-muted d-block uppercase fw-bold mb-1">Địa chỉ giao hàng:</label>
                    <div class="small lh-base fw-medium text-secondary">
                        <i class="bi bi-geo-alt text-danger me-2"></i>{{ $order['address'] }}
                    </div>
                </div>

                <div class="pt-3 border-top">
                    <label class="extra-small text-muted d-block uppercase fw-bold mb-2">Hình thức thanh toán:</label>
                    @if($order['payment_method'] == 'cod')
                        <span class="badge bg-warning-subtle text-warning border-0 px-3 py-2 rounded-pill fw-bold small d-inline-flex align-items-center">
                            <i class="bi bi-cash-stack me-2"></i> TIỀN MẶT (COD)
                        </span>
                    @else
                        <span class="badge bg-info-subtle text-info border-0 px-3 py-2 rounded-pill fw-bold small d-inline-flex align-items-center">
                            <i class="bi bi-credit-card me-2"></i> CHUYỂN KHOẢN
                        </span>
                    @endif
                </div>
            </div>

            <!-- Thao tác nhanh -->
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-dark text-white shadow-lg overflow-hidden position-relative">
                <!-- Background Decor -->
                <i class="bi bi-lightning-fill position-absolute text-white opacity-10" style="font-size: 8rem; right: -20px; bottom: -20px;"></i>

                <h6 class="fw-black mb-4 uppercase text-white opacity-50 small tracking-widest border-bottom border-secondary pb-2">Hành động nhanh</h6>
                
                @if($currentStatus == 0)
                    <a href="{{ rtrim(BASE_URL, '/') }}/order/cancel/{{ $order['id'] }}" 
                       class="btn btn-danger w-100 rounded-pill py-2.5 fw-bold mb-3 shadow-sm transition-all hover-lift"
                       onclick="return confirm('Xác nhận yêu cầu hủy đơn hàng này không?')">
                        <i class="bi bi-x-circle me-1"></i> HỦY ĐƠN HÀNG
                    </a>
                @endif

                <a href="{{ rtrim(BASE_URL, '/') }}/order/reorder/{{ $order['id'] }}" 
                   class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold mb-3 shadow-sm transition-all hover-lift">
                    <i class="bi bi-arrow-repeat me-1"></i> MUA LẠI ĐƠN NÀY
                </a>

                @if($currentStatus == 3)
                    <button class="btn btn-warning text-dark w-100 rounded-pill py-2.5 fw-bold mb-2 shadow-sm transition-all hover-lift">
                        <i class="bi bi-star-fill me-1"></i> VIẾT ĐÁNH GIÁ
                    </button>
                @endif
                
                <div class="text-center mt-3">
                    <a href="#" class="extra-small text-white opacity-50 text-decoration-none hover-underline italic">Cần hỗ trợ về đơn hàng? Chat với chúng tôi</a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 11px; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.8px; }
    .tracking-tighter { letter-spacing: -1.5px; }
    .bg-slate-50 { background-color: #f8fafc; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    .object-fit-contain { object-fit: contain; }
    
    /* Timeline Styles */
    .order-tracker .tracker-step { width: 25%; }
    .step-icon-wrapper { width: 54px; height: 54px; background: #fff; border: 4px solid #f1f5f9; color: #cbd5e1; transition: 0.4s; z-index: 2; }
    .tracker-step.active .step-icon-wrapper { background: #2563eb; color: #fff; border-color: #e7f1ff; transform: scale(1.1); }
    .tracker-step.active .step-label { color: #2563eb; }
    
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .transition-all { transition: all 0.3s ease; }
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes bounceIn { from { opacity: 0; transform: scale(0.9); } 50% { transform: scale(1.02); } to { opacity: 1; transform: scale(1); } }
    .animate-bounce-in { animation: bounceIn 0.5s ease-out; }
    
    .bg-primary-subtle { background-color: #e7f1ff !important; }
    .bg-warning-subtle { background-color: #fff9db !important; }
    .bg-info-subtle { background-color: #e7f5ff !important; }

    @media (max-width: 768px) {
        .order-tracker .step-label { font-size: 9px; }
        .order-tracker .tracker-step { width: auto; }
        .tracker-line { display: none; }
        .order-tracker { flex-direction: column; gap: 20px; align-items: flex-start; }
        .tracker-step { display: flex; align-items: center; gap: 15px; }
        .step-icon-wrapper { margin-bottom: 0 !important; }
    }
</style>

@include('user.layouts.footer')