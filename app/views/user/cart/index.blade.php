@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $cleanBaseUrl = rtrim(BASE_URL, '/');
    $cleanBaseUrl = str_replace(['/public/index.php', '/index.php'], '', $cleanBaseUrl);
    
    $cart = $_SESSION['cart'] ?? [];
@endphp

@include('user.layouts.header')

<div class="container py-4 text-dark">
    <div class="mb-5">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight uppercase d-flex align-items-center">
            <i class="bi bi-cart3 text-primary me-3"></i> Giỏ hàng của bạn
        </h1>
        <p class="text-muted">
            Bạn đang có <span class="fw-bold text-primary">{{ count($cart) }}</span> mặt hàng trong danh sách.
        </p>
    </div>

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 py-3 animate-slide-in">
            <div class="d-flex align-items-center fw-bold">
                <i class="bi bi-exclamation-circle-fill fs-4 me-3"></i>
                <span>{{ $_SESSION['error'] }}</span>
            </div>
            @php unset($_SESSION['error']) @endphp
        </div>
    @endif

    @if(empty($cart))
        <div class="bg-white p-5 rounded-5 shadow-sm text-center border border-slate-100 py-10">
            <div class="mb-4">
                <i class="bi bi-bag-x text-slate-200" style="font-size: 6rem;"></i>
            </div>
            <h3 class="text-slate-800 fw-bold mb-3">Giỏ hàng đang trống!</h3>
            <p class="text-slate-500 mb-4 mx-auto" style="max-width: 400px;">
                Có vẻ như bạn chưa chọn được phiên bản sản phẩm nào ưng ý. Hãy quay lại cửa hàng để khám phá các siêu phẩm công nghệ nhé!
            </p>
            <a href="{{ $cleanBaseUrl }}/product/index" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg text-white transition-all hover:-translate-y-1">
                TIẾP TỤC MUA SẮM
            </a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-slate-50 border-bottom">
                                <tr class="text-muted small fw-bold uppercase tracking-wider">
                                    <th class="ps-4 py-3 border-0">Sản phẩm & Phiên bản</th>
                                    <th class="text-center border-0">Đơn giá</th>
                                    <th class="text-center border-0">Số lượng</th>
                                    <th class="text-center border-0">Thành tiền</th>
                                    <th class="pe-4 border-0"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($cart as $key => $item)
                                <tr>
                                    <td class="ps-4 py-4">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-slate-50 rounded-4 p-1 me-3 border border-slate-100 position-relative group" style="width: 85px; height: 85px;">
                                                <img src="{{ $cleanBaseUrl }}/public/uploads/products/{{ $item['image'] ?: 'default.jpg' }}" 
                                                     class="w-100 h-100 object-fit-contain transition-transform group-hover:scale-110" 
                                                     alt="{{ $item['name'] }}"
                                                     onerror="this.src='https://placehold.co/100x100?text=SP'">
                                            </div>
                                            <div>
                                                <h6 class="mb-1 fw-bold text-dark fs-6">{{ $item['name'] }}</h6>
                                                
                                                @if(!empty($item['attributes']))
                                                    <div class="d-flex align-items-center gap-1">
                                                        <span class="badge bg-primary text-white border-0 rounded-pill px-3 py-1 fw-bold" style="font-size: 10px;">
                                                            <i class="bi bi-gear-fill me-1"></i> {{ $item['attributes'] }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="badge bg-light text-muted border rounded-pill px-3 py-1" style="font-size: 10px;">
                                                        Phiên bản mặc định
                                                    </span>
                                                @endif
                                                
                                                <div class="mt-1 extra-small text-slate-400">Mã đơn: #{{ $key }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-medium text-dark">
                                        {{ number_format($item['price'], 0, ',', '.') }}đ
                                    </td>

                                    <td class="text-center">
                                        <form action="{{ $cleanBaseUrl }}/cart/updateQuantity" method="POST" class="d-inline-block">
                                            <input type="hidden" name="id" value="{{ $key }}">
                                            <div class="input-group input-group-sm border rounded-pill overflow-hidden shadow-sm bg-white" style="width: 100px; margin: 0 auto;">
                                                <input type="number" name="quantity" 
                                                       class="form-control border-0 text-center fw-bold shadow-none py-2" 
                                                       value="{{ $item['quantity'] }}" min="1" 
                                                       onchange="this.form.submit()">
                                            </div>
                                        </form>
                                    </td>

                                    <td class="text-center fw-black text-primary fs-5 tracking-tighter">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}đ
                                    </td>

                                    <td class="text-end pe-4">
                                        <a href="{{ $cleanBaseUrl }}/cart/remove/{{ $key }}" 
                                           class="btn btn-link text-slate-300 hover:text-danger p-2 transition-all" 
                                           onclick="return confirm('Bạn muốn bỏ sản phẩm này khỏi giỏ hàng?')"
                                           title="Xóa khỏi giỏ">
                                            <i class="bi bi-trash3-fill fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3 bg-slate-50 border-top d-flex justify-content-between align-items-center">
                        <a href="{{ $cleanBaseUrl }}/product/index" class="btn btn-link text-decoration-none text-slate-600 fw-bold small transition-all hover:text-primary">
                            <i class="bi bi-arrow-left me-2"></i> TIẾP TỤC CHỌN SẢN PHẨM
                        </a>
                        <a href="{{ $cleanBaseUrl }}/cart/clear" class="btn btn-link text-decoration-none text-danger fw-bold small" 
                           onclick="return confirm('Toàn bộ giỏ hàng sẽ bị xóa sạch, bạn chắc chứ?')">
                           LÀM TRỐNG GIỎ HÀNG
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 text-dark">
                <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                    <div class="card-body p-4">
                        <h6 class="fw-black text-dark uppercase mb-3 small tracking-wider">Mã giảm giá MD</h6>
                        <form action="{{ $cleanBaseUrl }}/cart/applyCoupon" method="POST">
                            <div class="input-group">
                                <input type="text" name="coupon_code" 
                                       class="form-control rounded-start-4 shadow-none border-slate-200 text-uppercase fw-bold py-2.5" 
                                       placeholder="Nhập mã ưu đãi..." 
                                       value="{{ $coupon['code'] ?? '' }}" required>
                                <button class="btn btn-dark rounded-end-4 px-3 fw-bold" type="submit">ÁP DỤNG</button>
                            </div>
                        </form>

                        @if(isset($coupon))
                            <div class="mt-3 p-3 bg-green-50 border border-green-100 rounded-4 animate-fade-in">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-green-700 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> {{ $coupon['code'] }}
                                    </span>
                                    <a href="{{ $cleanBaseUrl }}/cart/removeCoupon" class="text-danger small text-decoration-none fw-bold hover:underline">GỠ BỎ</a>
                                </div>
                                <div class="text-green-600 extra-small mt-1 italic">
                                    Tiết kiệm: {{ number_format($discount ?? 0, 0, ',', '.') }}đ
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card border-0 shadow-lg rounded-5 text-white overflow-hidden" style="background-color: #0f172a;">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-black text-center mb-5 uppercase tracking-tighter">HÓA ĐƠN TẠM TÍNH</h4>
                        
                        <div class="d-flex justify-content-between mb-3 text-slate-400">
                            <span>Tổng giá trị hàng:</span>
                            <span class="fw-bold text-white">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3 text-green-400">
                            <span>Ưu đãi giảm giá:</span>
                            <span class="fw-bold">-{{ number_format($discount ?? 0, 0, ',', '.') }}đ</span>
                        </div>

                        <div class="d-flex justify-content-between mb-5 text-slate-400">
                            <span>Phí giao hàng:</span>
                            <span class="text-success fw-bold small uppercase"><i class="bi bi-truck me-1"></i> Miễn phí</span>
                        </div>

                        <hr class="border-white opacity-10 my-4">

                        <div class="text-center mb-5">
                            <span class="d-block text-slate-400 small uppercase tracking-widest mb-1">TỔNG THANH TOÁN</span>
                            <span class="text-primary display-5 fw-black tracking-tighter">{{ number_format($total, 0, ',', '.') }}đ</span>
                        </div>

                        <a href="{{ $cleanBaseUrl }}/order/checkout" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg shadow-blue-500/20 text-white transition-all hover:-translate-y-1">
                            THANH TOÁN AN TOÀN <i class="bi bi-shield-check ms-2"></i>
                        </a>
                        
                        <p class="text-center text-slate-500 extra-small mt-4 italic mb-0">
                            * Giá đã bao gồm VAT và bảo hiểm vận chuyển.
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
    .tracking-tighter { letter-spacing: -1.5px; }
    
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .animate-fade-in { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; opacity: 1; } }

    .group:hover img { transform: scale(1.1); }
    
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { opacity: 1; }
</style>

@include('user.layouts.footer')