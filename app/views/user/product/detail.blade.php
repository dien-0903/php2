@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // --- GIẢI PHÁP TÁCH BIỆT URL (XỬ LÝ TRIỆT ĐỂ) ---
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    // 1. URL CHO CHỨC NĂNG (Nút bấm, Link)
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    $path = str_replace('/public', '', $scriptDir);
    $path = rtrim($path, '/');
    $LINK_URL = $protocol . $host . $path;

    // 2. URL CHO HÌNH ẢNH: Dùng cứng 'localhost' theo yêu cầu của bạn
    $IMG_URL = 'http://localhost/PHP2';
@endphp

@include('user.layouts.header')

<!-- GIAO DIỆN SẠCH (CLEAN DESIGN) -->
<div class="product-detail-wrapper bg-light min-vh-100 font-inter text-dark">
    
    <div class="container py-5">
        
        <!-- Alert Success -->
        @if(isset($_SESSION['success']))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4 animate-bounce-in bg-white">
                <div class="d-flex align-items-center text-success fw-bold">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <span>{{ $_SESSION['success'] }}</span>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
            </div>
            @php unset($_SESSION['success']) @endphp
        @endif

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-transparent p-0 small">
                <li class="breadcrumb-item"><a href="{{ $LINK_URL }}/" class="text-decoration-none text-muted">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ $LINK_URL }}/product/index" class="text-decoration-none text-muted">Sản phẩm</a></li>
                <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $product['name'] }}</li>
            </ol>
        </nav>

        <!-- Main Product Section -->
        <div class="bg-white rounded-5 shadow-sm border border-light-subtle overflow-hidden mb-5">
            <div class="row g-0">
                
                <!-- Left Column: Gallery (Hiển thị nhiều ảnh) -->
                <div class="col-lg-6 p-4 p-lg-5 border-end border-light-subtle bg-white">
                    <div class="position-relative mb-4 d-flex align-items-center justify-content-center bg-light rounded-4 overflow-hidden" style="height: 500px;">
                        @php        
                            $imagePath = $IMG_URL . '/public/uploads/products/' . ($product['image'] ?: 'default.jpg');
                        @endphp
                        
                        <img id="main-product-image" 
                             src="{{ $imagePath }}" 
                             class="img-fluid main-img transition-all" 
                             style="max-height: 450px; object-fit: contain;"
                             alt="{{ $product['name'] }}"
                             onerror="this.src='https://placehold.co/600x600?text=No+Image'">
                        
                        <div class="position-absolute top-0 start-0 m-3">
                            <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                                <i class="bi bi-patch-check-fill me-1"></i> CHÍNH HÃNG 100%
                            </span>
                        </div>
                    </div>

                    <!-- DANH SÁCH ẢNH THƯ VIỆN (GALLERY THUMBNAILS) -->
                    @if(!empty($product['gallery']) || !empty($product['image']))
                    <div class="row g-2 justify-content-center" id="product-thumbnails">
                        <div class="col-2">
                            <div class="thumb-item active border rounded-3 overflow-hidden cursor-pointer transition-all shadow-sm" 
                                 onclick="changeImage('{{ $imagePath }}', this)">
                                <div class="ratio ratio-1x1">
                                    <img src="{{ $imagePath }}" class="object-fit-cover w-100 h-100" alt="main-thumb">
                                </div>
                            </div>
                        </div>

                        @if(!empty($product['gallery']))
                            @foreach($product['gallery'] as $gal)
                                @php $galPath = $IMG_URL . '/public/uploads/products/' . $gal['image']; @endphp
                                <div class="col-2">
                                    <div class="thumb-item border rounded-3 overflow-hidden cursor-pointer transition-all shadow-sm" 
                                         onclick="changeImage('{{ $galPath }}', this)">
                                        <div class="ratio ratio-1x1">
                                            <img src="{{ $galPath }}" class="object-fit-cover w-100 h-100" alt="gallery-thumb">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @endif
                </div>

                <!-- Right Column: Info -->
                <div class="col-lg-6 p-4 p-lg-5 d-flex flex-column bg-white">
                    <div class="mb-auto">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="text-primary fw-bold text-xs text-uppercase tracking-widest">
                                {{ $product['category_name'] ?? 'Công nghệ' }}
                            </span>
                            <span class="text-secondary opacity-25">|</span>
                            <div class="text-warning small">
                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                                <span class="text-muted ms-1">(4.8/5)</span>
                            </div>
                        </div>

                        <h1 class="display-6 fw-black text-dark mb-2 tracking-tight uppercase" style="line-height: 1.2;">
                            {{ $product['name'] }}
                        </h1>
                        
                        <div class="d-flex align-items-baseline gap-3 mb-4">
                            <h2 id="main-price" class="text-danger fw-black display-5 mb-0">
                                {{ number_format($product['price'], 0, ',', '.') }}đ
                            </h2>
                        </div>

                        <!-- Variants Section -->
                        @if(!empty($variants))
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label fw-bold small text-muted uppercase tracking-wider mb-0">Chọn phiên bản:</label>
                                    <span id="stock-label" class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">
                                        Đang kiểm tra kho...
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-wrap gap-2" id="variant-list">
                                    @foreach($variants as $index => $v)
                                        @php
                                            $vImgName = $v['image'] ?: ($product['image'] ?: 'default.jpg');
                                            $variantImagePath = $IMG_URL . '/public/uploads/products/' . $vImgName;
                                        @endphp
                                        <div class="variant-item position-relative" style="min-width: 180px; flex-grow: 1;">
                                            <input type="radio" class="btn-check" name="product_variant" id="v-{{ $v['id'] }}" 
                                                   autocomplete="off" {{ $index === 0 ? 'checked' : '' }}>
                                            
                                            <label class="btn btn-outline-light text-dark w-100 rounded-3 p-2 text-start variant-card transition-all border d-flex align-items-center gap-3" 
                                                   for="v-{{ $v['id'] }}"
                                                   data-price="{{ number_format($v['price'], 0, ',', '.') }}đ"
                                                   data-image="{{ $variantImagePath }}"
                                                   data-stock="{{ $v['stock'] }}"
                                                   data-variant-id="{{ $v['id'] }}">
                                                
                                                <div class="rounded-2 overflow-hidden bg-white border flex-shrink-0" style="width: 45px; height: 45px;">
                                                    <img src="{{ $variantImagePath }}" class="w-100 h-100 object-fit-cover" alt="variant">
                                                </div>

                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="fw-bold text-dark name-label small">{{ $v['color_name'] ?: 'Tiêu chuẩn' }}</div>
                                                        <i class="bi bi-check-circle-fill check-icon text-primary d-none" style="font-size: 0.8rem;"></i>
                                                    </div>
                                                    <div class="text-muted extra-small" style="font-size: 0.75rem;">{{ $v['size_name'] ?: 'Size chuẩn' }}</div>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-4">
                                @if(isset($product['stock']) && $product['stock'] > 0)
                                    <span class="badge bg-success-subtle text-success border-0 px-3 py-2 rounded-pill fw-bold">
                                        <i class="bi bi-check-circle me-1"></i> Còn hàng ({{ $product['stock'] }})
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border-0 px-3 py-2 rounded-pill fw-bold">
                                        <i class="bi bi-x-circle me-1"></i> Hết hàng
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="bg-light border rounded-4 p-4 mt-4">
                            <h6 class="fw-bold text-primary mb-1 small uppercase"><i class="bi bi-truck me-2"></i>Chính sách MD</h6>
                            <p class="small text-secondary mb-0">Miễn phí giao hàng hỏa tốc trong 2h tại nội thành.</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-4 border-top">
                        <div class="row g-2">
                            @php
                                $isOutOfStock = false;
                                if (empty($variants) && isset($product['stock']) && $product['stock'] <= 0) {
                                    $isOutOfStock = true;
                                }
                            @endphp

                            <div class="col-12 col-md-6 mb-2 mb-md-0">
                                <a id="add-to-cart-link" 
                                   href="{{ $isOutOfStock ? 'javascript:void(0)' : $LINK_URL . '/cart/add/' . $product['id'] }}" 
                                   class="btn {{ $isOutOfStock ? 'btn-secondary disabled' : 'btn-primary' }} w-100 rounded-pill py-3 fw-bold shadow-lg text-white transition-all h-100 d-flex align-items-center justify-content-center">
                                    @if($isOutOfStock)
                                        <span>HẾT HÀNG</span>
                                    @else
                                        <i class="bi bi-cart-plus-fill me-2"></i> THÊM VÀO GIỎ
                                    @endif
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                 <a href="{{ $LINK_URL }}/wishlist/add/{{ $product['id'] }}" 
                                    class="btn btn-outline-danger w-100 rounded-pill py-3 transition-all fw-bold h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-heart me-1"></i> <span class="d-none d-lg-inline ms-1">Yêu thích</span>
                                 </a>
                            </div>
                            <div class="col-6 col-md-3">
                                 <a href="{{ $LINK_URL }}/compare/add/{{ $product['id'] }}" 
                                    class="btn btn-outline-dark w-100 rounded-pill py-3 transition-all fw-bold h-100 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-arrow-left-right me-1"></i> <span class="d-none d-lg-inline ms-1">So sánh</span>
                                 </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs: Description & Specs -->
        <div class="card border-0 shadow-sm rounded-5 overflow-hidden bg-white mb-5">
            <div class="card-body p-5">
                <ul class="nav nav-tabs border-0 gap-4 mb-4" id="productTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active border-0 fw-bold px-0 text-dark position-relative after-line" data-bs-toggle="tab" data-bs-target="#desc" type="button">MÔ TẢ CHI TIẾT</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link border-0 fw-bold px-0 text-muted" data-bs-toggle="tab" data-bs-target="#spec" type="button">THÔNG SỐ KỸ THUẬT</button>
                    </li>
                </ul>
                <div class="tab-content pt-2" id="productTabContent">
                    <div class="tab-pane fade show active" id="desc">
                        <div class="text-muted leading-loose" style="white-space: pre-line;">
                            {!! !empty($product['description']) ? $product['description'] : 'Sản phẩm đang được cập nhật nội dung mô tả chi tiết.' !!}
                        </div>
                    </div>
                    <div class="tab-pane fade" id="spec">
                        <table class="table table-striped rounded-4 overflow-hidden border border-slate-100">
                            <tbody class="text-sm">
                                <tr><th width="30%" class="ps-4 py-3 bg-light">Thương hiệu</th><td class="ps-3">{{ $product['brand_name'] ?? 'Chính hãng' }}</td></tr>
                                <tr><th class="ps-4 py-3 bg-light">Phân loại</th><td class="ps-3">{{ $product['category_name'] ?? 'Thiết bị' }}</td></tr>
                                <tr><th class="ps-4 py-3 bg-light">Tình trạng</th><td class="ps-3">Mới 100%, Nguyên Seal</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- PHẦN THÊM LẠI: Sản phẩm liên quan -->
        @if(isset($relatedProducts) && count($relatedProducts) > 0)
        <div class="mb-5">
            <h3 class="fw-black text-dark mb-4 uppercase tracking-tight ps-3 border-start border-4 border-primary">Sản phẩm liên quan</h3>
            <div class="row g-4">
                @foreach($relatedProducts as $item)
                    <div class="col-6 col-md-3">
                        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden hover:shadow-lg transition-all group">
                            <a href="{{ $LINK_URL }}/product/show/{{ $item['id'] }}" class="text-decoration-none">
                                <div class="bg-white p-4 d-flex align-items-center justify-content-center relative overflow-hidden" style="height: 220px;">
                                    @php $relImgPath = $IMG_URL . '/public/uploads/products/' . ($item['image'] ?: 'default.jpg'); @endphp
                                    <img src="{{ $relImgPath }}" 
                                         class="img-fluid transition-transform group-hover:scale-105" 
                                         style="max-height: 100%; width: auto; object-fit: contain;"
                                         onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                                </div>
                                <div class="card-body text-center pt-0 pb-3">
                                    <h6 class="fw-bold text-dark text-truncate small">{{ $item['name'] }}</h6>
                                    <p class="text-danger fw-bold mb-0 small">{{ number_format($item['price'], 0, ',', '.') }}đ</p>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- PHẦN THÊM LẠI: Sản phẩm đã xem gần đây -->
        @if(isset($recentProducts) && count($recentProducts) > 0)
        <div class="mb-5">
            <h3 class="fw-black text-dark mb-4 uppercase tracking-tight ps-3 border-start border-4 border-info">Đã xem gần đây</h3>
            <div class="row g-4">
                @foreach($recentProducts as $item)
                    <div class="col-6 col-md-2">
                        <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden bg-white hover:shadow-md transition-all">
                            <a href="{{ $LINK_URL }}/product/show/{{ $item['id'] }}" class="text-decoration-none">
                                <div class="p-3 d-flex align-items-center justify-content-center" style="height: 120px;">
                                    @php $recImgPath = $IMG_URL . '/public/uploads/products/' . ($item['image'] ?: 'default.jpg'); @endphp
                                    <img src="{{ $recImgPath }}" class="img-fluid" style="max-height: 100%; object-fit: contain;" onerror="this.src='https://placehold.co/200x200?text=No+Image'">
                                </div>
                                <div class="p-2 text-center border-top border-light">
                                    <div class="text-truncate extra-small fw-bold text-dark">{{ $item['name'] }}</div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>

<script>
/**
 * Hàm thay đổi ảnh chính khi nhấn vào ảnh nhỏ (Gallery)
 */
function changeImage(src, element) {
    const mainImg = document.getElementById('main-product-image');
    if(!mainImg) return;

    mainImg.style.opacity = '0.3';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 150);

    document.querySelectorAll('.thumb-item').forEach(item => {
        item.classList.remove('active', 'border-primary');
    });
    if(element && element.classList) {
        element.classList.add('active', 'border-primary');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const variantInputs = document.querySelectorAll('input[name="product_variant"]');
    const mainPrice = document.getElementById('main-price');
    const stockLabel = document.getElementById('stock-label');
    const cartBtn = document.getElementById('add-to-cart-link');
    const productId = "{{ $product['id'] }}";
    const appUrl = "{{ $LINK_URL }}";

    const checkedVariant = document.querySelector('input[name="product_variant"]:checked');
    if(checkedVariant) {
        updateUI(document.querySelector(`label[for="${checkedVariant.id}"]`));
    }

    variantInputs.forEach(input => {
        input.addEventListener('change', function() {
            const label = document.querySelector(`label[for="${this.id}"]`);
            updateUI(label);
        });
    });

    function updateUI(label) {
        if(!label) return;
        mainPrice.innerText = label.dataset.price;
        if (label.dataset.image) {
            changeImage(label.dataset.image, null); 
        }
        const stock = parseInt(label.dataset.stock);
        const variantId = label.dataset.variantId;

        if(stock > 0) {
            if(stockLabel) {
                stockLabel.innerHTML = `<i class="bi bi-check2"></i> Còn ${stock} sản phẩm`;
                stockLabel.className = "badge bg-success-subtle text-success border-0 px-2 py-1";
            }
            if(cartBtn) {
                cartBtn.href = `${appUrl}/cart/add/${productId}/${variantId}`;
                cartBtn.classList.remove('disabled', 'btn-secondary');
                cartBtn.classList.add('btn-primary');
                cartBtn.style.pointerEvents = 'auto'; 
                cartBtn.innerHTML = '<i class="bi bi-cart-plus-fill me-2"></i> THÊM VÀO GIỎ';
            }
        } else {
            if(stockLabel) {
                stockLabel.innerText = "Tạm hết hàng";
                stockLabel.className = "badge bg-danger-subtle text-danger border-0 px-2 py-1";
            }
            if(cartBtn) {
                cartBtn.href = "javascript:void(0)";
                cartBtn.classList.add('disabled', 'btn-secondary');
                cartBtn.classList.remove('btn-primary');
                cartBtn.style.pointerEvents = 'none';
                cartBtn.innerHTML = 'HẾT HÀNG';
            }
        }
    }
});
</script>

<style>
    .font-inter { font-family: 'Inter', sans-serif; }
    .fw-black { font-weight: 900; }
    .leading-loose { line-height: 2; }
    .cursor-pointer { cursor: pointer; }
    .main-img { transition: opacity 0.2s ease-in-out; }
    .thumb-item { border: 2px solid #eee !important; background: #fff; transition: all 0.3s ease; opacity: 0.6; }
    .thumb-item:hover { opacity: 1; transform: translateY(-2px); border-color: #ddd !important; }
    .thumb-item.active { opacity: 1; border-color: #0d6efd !important; box-shadow: 0 0 10px rgba(13, 110, 253, 0.2); }
    .variant-card { border-color: #e2e8f0 !important; background-color: #ffffff; transition: all 0.2s ease-in-out; }
    .variant-card:hover { transform: translateY(-2px); border-color: #cbd5e1 !important; }
    .btn-check:checked + .variant-card { border-color: #2563eb !important; background-color: #eff6ff !important; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1); }
    .btn-check:checked + .variant-card .name-label { color: #2563eb !important; }
    .btn-check:checked + .variant-card .check-icon { display: block !important; }
    .nav-tabs .nav-link.active { background: transparent !important; color: #2563eb !important; }
    .after-line.active::after { content: ''; position: absolute; bottom: -5px; left: 0; width: 100%; height: 3px; background: #2563eb; border-radius: 10px; }
    @keyframes bounceIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-bounce-in { animation: bounceIn 0.5s ease-out; }
    @media (max-width: 768px) { .variant-item { width: 100%; } }
</style>

@include('user.layouts.footer')