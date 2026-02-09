@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $path = str_replace('/public', '', $scriptDir);
    $path = rtrim($path, '/');
    $LINK_URL = $protocol . $host . $path;
    $IMG_URL = 'http://localhost/PHP2';
@endphp

@include('user.layouts.header')

<div class="wishlist-wrapper bg-light min-vh-100 font-inter text-dark py-5">
    <div class="container">
        
        <div class="text-center mb-5">
            <h1 class="display-6 fw-black text-uppercase tracking-tight mb-2 text-dark">
                Sản phẩm yêu thích
            </h1>
            <div class="d-inline-block bg-white text-secondary px-4 py-2 rounded-pill shadow-sm small fw-bold border">
                <i class="bi bi-heart-fill text-danger me-1"></i> 
                @if(!empty($wishlist_ids))
                    Bạn đang quan tâm {{ count($wishlist_ids) }} sản phẩm
                @else
                    Danh sách trống
                @endif
            </div>
        </div>

        @if(isset($_SESSION['success']))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-5 d-flex align-items-center justify-content-center gap-2 animate-bounce-in bg-white text-success fw-bold">
                <i class="bi bi-check-circle-fill"></i>
                {{ $_SESSION['success'] }}
                @php unset($_SESSION['success']) @endphp
            </div>
        @endif

        @if(empty($wishlist_ids))
            <div class="text-center py-5">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-sm p-4" style="width: 100px; height: 100px;">
                        <i class="bi bi-heartbreak text-secondary" style="font-size: 2.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-2">Chưa có sản phẩm nào!</h4>
                <p class="text-muted mb-4">Hãy thêm những món đồ bạn thích vào đây để xem lại sau nhé.</p>
                <a href="{{ $LINK_URL }}/product/index" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg transition-all hover-up">
                    <i class="bi bi-shop me-2"></i> Mua sắm ngay
                </a>
            </div>
        @else
            
            @if(empty($products))
                <div class="alert alert-warning rounded-4 border-0 shadow-sm text-center">
                    <i class="bi bi-info-circle me-2"></i> Đang hiển thị danh sách ID (Cần cập nhật Controller để load chi tiết sản phẩm).
                </div>
                <div class="row g-4">
                    @foreach($wishlist_ids as $id)
                    <div class="col-6 col-md-3">
                        <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4 bg-white">
                            <div class="text-muted mb-3">ID: <strong>{{ $id }}</strong></div>
                            <div class="d-grid gap-2">
                                <a href="{{ $LINK_URL }}/product/show/{{ $id }}" class="btn btn-outline-primary rounded-pill btn-sm">Xem ngay</a>
                                <a href="{{ $LINK_URL }}/wishlist/remove/{{ $id }}" class="btn btn-light rounded-pill btn-sm text-danger hover-bg-danger">Xóa</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="row g-4">
                    @foreach($products as $item)
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden product-card hover-shadow-lg transition-all group bg-white position-relative">
                                
                                <a href="{{ $LINK_URL }}/wishlist/remove/{{ $item['id'] }}" 
                                   class="position-absolute top-0 end-0 m-3 btn btn-white rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-scale z-2"
                                   style="width: 35px; height: 35px; background: rgba(255,255,255,0.9);"
                                   title="Xóa khỏi yêu thích">
                                    <i class="bi bi-trash"></i>
                                </a>

                                <a href="{{ $LINK_URL }}/product/show/{{ $item['id'] }}" class="text-decoration-none d-block overflow-hidden bg-light">
                                    <div class="ratio ratio-1x1">
                                        <div class="d-flex align-items-center justify-content-center p-4">
                                            @php
                                                $imgName = $item['image'] ?: 'default.jpg';
                                                $imgPath = $IMG_URL . '/public/uploads/products/' . $imgName;
                                            @endphp
                                            <img src="{{ $imgPath }}" 
                                                 class="mw-100 mh-100 transition-transform duration-500 group-hover:scale-110" 
                                                 alt="{{ $item['name'] }}"
                                                 onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                                        </div>
                                    </div>
                                </a>

                                <div class="card-body text-center d-flex flex-column p-3">
                                    <div class="mb-auto">
                                        <h6 class="card-title fw-bold text-dark text-truncate mb-1" style="font-size: 0.95rem;">
                                            <a href="{{ $LINK_URL }}/product/show/{{ $item['id'] }}" class="text-decoration-none text-dark stretched-link">
                                                {{ $item['name'] }}
                                            </a>
                                        </h6>
                                        <p class="text-danger fw-black mb-3">{{ number_format($item['price'], 0, ',', '.') }}đ</p>
                                    </div>
                                    
                                    <div class="d-grid position-relative z-1">
                                        @if(isset($item['stock']) && $item['stock'] > 0)
                                            <a href="{{ $LINK_URL }}/cart/add/{{ $item['id'] }}" class="btn btn-primary rounded-pill fw-bold btn-sm py-2">
                                                <i class="bi bi-cart-plus me-1"></i> Thêm vào giỏ
                                            </a>
                                        @else
                                            <a href="javascript:void(0)" class="btn btn-secondary rounded-pill fw-bold btn-sm py-2 disabled" style="pointer-events: none; opacity: 0.6;">
                                                HẾT HÀNG
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        @endif
    </div>
</div>

@include('user.layouts.footer')