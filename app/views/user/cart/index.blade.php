@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $cleanBaseUrl = rtrim(BASE_URL, '/');
    $cleanBaseUrl = str_replace('/public/index.php', '', $cleanBaseUrl);
    $cleanBaseUrl = str_replace('/index.php', '', $cleanBaseUrl);
@endphp

@include('user.layouts.header')

<div class="mb-5">
    <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase mb-4">
        <i class="bi bi-cart3 text-primary me-2"></i>Giỏ hàng của bạn
    </h1>
    <p class="text-slate-500 mb-5">Kiểm tra danh sách sản phẩm và áp dụng mã giảm giá trước khi thanh toán.</p>

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4 animate-slide-in">
            <div class="d-flex align-items-center text-danger fw-bold">
                <i class="bi bi-exclamation-circle-fill fs-4 me-3"></i>
                <span>{{ $_SESSION['error'] }}</span>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['error']) @endphp
        </div>
    @endif

    @if(isset($_SESSION['success']))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4 animate-slide-in">
            <div class="d-flex align-items-center text-success fw-bold">
                <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                <span>{{ $_SESSION['success'] }}</span>
            </div>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    @if(empty($cart))
        <div class="bg-white p-10 rounded-5 shadow-sm text-center border border-slate-50">
            <div class="mb-4 text-slate-200">
                <i class="bi bi-bag-x fs-1" style="font-size: 5rem !important;"></i>
            </div>
            <h4 class="text-slate-500 font-bold mb-3">Giỏ hàng của bạn đang trống!</h4>
            <p class="text-slate-400 mb-4">Có vẻ như bạn chưa thêm sản phẩm nào vào giỏ hàng.</p>
            <a href="{{ $cleanBaseUrl }}/product/index" class="btn btn-primary rounded-pill px-5 py-3 font-bold shadow-lg shadow-blue-100 text-white">
                QUAY LẠI CỬA HÀNG NGAY
            </a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden text-dark">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="ps-4 py-3 border-0 small fw-bold text-slate-500">SẢN PHẨM</th>
                                    <th class="text-center border-0 small fw-bold text-slate-500">GIÁ</th>
                                    <th class="text-center border-0 small fw-bold text-slate-500">SỐ LƯỢNG</th>
                                    <th class="text-center border-0 small fw-bold text-slate-500">TỔNG</th>
                                    <th class="text-end pe-4 border-0"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($cart as $id => $item)
                                <tr>
                                    <td class="ps-4 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-slate-50 rounded-3 p-1 me-3 border border-slate-100" style="width: 70px; height: 70px;">
                                                <img src="{{ !empty($item['image']) ? $cleanBaseUrl . '/public/uploads/products/' . $item['image'] : 'https://placehold.co/100x100?text=No+Image' }}" 
                                                     class="w-100 h-100 object-fit-contain" alt="{{ $item['name'] }}">
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">{{ $item['name'] }}</h6>
                                                <small class="text-slate-400">ID: #{{ $id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-medium">{{ number_format($item['price'], 0, ',', '.') }}đ</td>
                                    <td class="text-center">
                                        <form action="{{ $cleanBaseUrl }}/cart/updateQuantity" method="POST" class="d-flex justify-content-center">
                                            <input type="hidden" name="id" value="{{ $id }}">
                                            <div class="input-group input-group-sm" style="width: 100px;">
                                                <input type="number" name="quantity" class="form-control text-center rounded-3 shadow-none border-slate-200" 
                                                       value="{{ $item['quantity'] }}" min="1" onchange="this.form.submit()">
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-center fw-black text-primary">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ $cleanBaseUrl }}/cart/remove/{{ $id }}" class="btn btn-link text-slate-300 hover:text-rose-500 p-0" 
                                           onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                                            <i class="bi bi-trash3-fill fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3 bg-slate-50 d-flex justify-content-between align-items-center">
                        <a href="{{ $cleanBaseUrl }}/product/index" class="btn btn-link text-decoration-none text-slate-600 fw-bold small">
                            <i class="bi bi-arrow-left me-1"></i> TIẾP TỤC MUA SẮM
                        </a>
                        <a href="{{ $cleanBaseUrl }}/cart/clear" class="btn btn-link text-decoration-none text-rose-500 fw-bold small" 
                           onclick="return confirm('Xóa toàn bộ giỏ hàng?')">XÓA TẤT CẢ</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4 text-dark">
                    <div class="card-body p-4">
                        <h6 class="fw-black text-dark uppercase mb-3 small tracking-wider">MÃ GIẢM GIÁ (COUPON)</h6>
                        
                        <form action="{{ $cleanBaseUrl }}/cart/applyCoupon" method="POST">
                            <div class="input-group">
                                <input type="text" name="coupon_code" class="form-control rounded-start-3 shadow-none border-slate-200 text-uppercase fw-bold" 
                                       placeholder="Nhập mã ưu đãi..." value="{{ $coupon['code'] ?? '' }}" required>
                                <button class="btn btn-dark rounded-end-3 px-3 fw-bold" type="submit">ÁP DỤNG</button>
                            </div>
                        </form>

                        @if(isset($coupon))
                            <div class="mt-3 p-3 bg-green-50 border border-green-100 rounded-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-green-700 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> ĐÃ ÁP DỤNG: {{ $coupon['code'] }}
                                    </span>
                                    <a href="{{ $cleanBaseUrl }}/cart/removeCoupon" class="text-rose-500 small text-decoration-none fw-bold hover:underline">
                                        <i class="bi bi-x-lg me-1"></i>HỦY
                                    </a>
                                </div>
                                <div class="text-green-600 extra-small mt-1 italic">
                                    (Tiết kiệm được: {{ number_format($discount ?? 0, 0, ',', '.') }}đ)
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-lg rounded-4 text-dark overflow-hidden">
                    <div class="card-body p-4 pt-5">
                        <h5 class="fw-black text-center mb-4 uppercase tracking-tighter">TÓM TẮT ĐƠN HÀNG</h5>
                        
                        <div class="d-flex justify-content-between mb-3 text-slate-500">
                            <span>Tạm tính:</span>
                            <span class="fw-bold text-dark">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-green-600">
                            <span>Giảm giá:</span>
                            <span class="fw-bold">-{{ number_format($discount ?? 0, 0, ',', '.') }}đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-4 text-slate-500">
                            <span>Phí vận chuyển:</span>
                            <span class="text-success fw-bold small uppercase">Miễn phí</span>
                        </div>

                        <hr class="border-slate-100 my-4">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fs-5 fw-bold">TỔNG CỘNG:</span>
                            <span class="text-primary fs-2 fw-black tracking-tighter">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>

                        <a href="{{ $cleanBaseUrl }}/order/checkout" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg shadow-blue-200 text-white transition-all hover:-translate-y-1">
                            TIẾN HÀNH THANH TOÁN <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        
                        <p class="text-center text-slate-400 extra-small mt-3 italic mb-0">
                            Giá đã bao gồm thuế VAT (nếu có)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 10px; }
    .object-fit-contain { object-fit: contain; }
    .text-uppercase { text-transform: uppercase; }
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

@include('user.layouts.footer')