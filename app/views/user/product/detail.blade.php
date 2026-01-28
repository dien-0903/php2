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
            <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/" class="text-decoration-none text-muted">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="text-decoration-none text-muted">Sản phẩm</a></li>
            <li class="breadcrumb-item active text-dark fw-bold" aria-current="page">{{ $product['name'] }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-5 shadow-sm border border-slate-50 overflow-hidden mb-5">
        <div class="row g-0">
            <div class="col-md-6 bg-slate-50 p-5 d-flex align-items-center justify-content-center relative group">
                <img id="main-product-image" src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $product['image'] ?? 'default.jpg' }}" 
                     class="img-fluid max-h-[450px] transition-transform duration-500 group-hover:scale-105" 
                     alt="{{ $product['name'] }}"
                     onerror="this.src='https://placehold.co/600x600?text=MD+Image'">
                
                <div class="absolute top-4 left-4">
                    <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-patch-check-fill me-1"></i> CHÍNH HÃNG 100%
                    </span>
                </div>
            </div>

            <div class="col-md-6 p-5 d-flex flex-column">
                <div class="mb-auto">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="text-blue-600 fw-bold text-xs text-uppercase tracking-widest">
                            {{ $product['category_name'] ?? 'Công nghệ' }}
                        </span>
                        <span class="text-slate-300">|</span>
                        <div class="text-warning small">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                            <span class="text-muted ms-1">(4.8/5)</span>
                        </div>
                    </div>

                    <h1 class="display-6 fw-black text-slate-900 mb-2 tracking-tighter uppercase">
                        {{ $product['name'] }}
                    </h1>
                    
                    <div class="d-flex align-items-baseline gap-3 mb-4">
                        <h2 id="main-price" class="text-rose-600 fw-black display-6 mb-0">
                            {{ number_format($product['price'], 0, ',', '.') }}đ
                        </h2>
                    </div>

                    @if(!empty($variants))
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold small text-muted uppercase tracking-wider mb-0">Chọn phiên bản:</label>
                                <span id="stock-label" class="badge bg-success-subtle text-success border-0 px-2 py-1" style="font-size: 10px;">
                                    Đang tải...
                                </span>
                            </div>
                            
                            <div class="d-flex flex-wrap gap-3" id="variant-list">
                                @foreach($variants as $index => $v)
                                    <div class="variant-item flex-fill" style="min-width: 140px;">
                                        <input type="radio" class="btn-check" name="product_variant" id="v-{{ $v['id'] }}" 
                                               autocomplete="off" {{ $index === 0 ? 'checked' : '' }}>
                                        <label class="btn btn-outline-light w-100 rounded-4 p-3 text-start variant-card transition-all border-2" 
                                               for="v-{{ $v['id'] }}"
                                               data-price="{{ number_format($v['price'], 0, ',', '.') }}đ"
                                               data-image="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $v['image'] ?: ($product['image'] ?: 'default.jpg') }}"
                                               data-stock="{{ $v['stock'] }}"
                                               data-variant-id="{{ $v['id'] }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="fw-bold text-dark name-label">{{ $v['color_name'] ?: 'Bản tiêu chuẩn' }}</div>
                                                <i class="bi bi-check-circle-fill check-icon text-primary d-none"></i>
                                            </div>
                                            <div class="text-muted small">{{ $v['size_name'] ?: 'Tiêu chuẩn' }}</div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <span class="badge bg-success-subtle text-success border-0 px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-check-circle me-1"></i> Sản phẩm có sẵn
                            </span>
                        </div>
                    @endif

                    <div class="bg-blue-50 border border-blue-100 rounded-4 p-4 mt-4">
                        <h6 class="fw-bold text-blue-800 mb-1 small uppercase"><i class="bi bi-truck me-2"></i>Chính sách MD</h6>
                        <p class="small text-blue-700 mb-0">Miễn phí giao hàng hỏa tốc trong 2h tại nội thành.</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-top">
                    <div class="row g-3">
                        <div class="col-8">
                            <a id="add-to-cart-link" href="{{ rtrim(BASE_URL, '/') }}/cart/add/{{ $product['id'] }}" 
                               class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-lg text-white transition-all hover:bg-blue-700">
                                <i class="bi bi-cart-plus-fill me-2"></i> THÊM VÀO GIỎ HÀNG
                            </a>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-outline-secondary btn-lg w-100 rounded-pill py-3 transition-all hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-5 overflow-hidden bg-white">
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
                    <div class="text-muted leading-loose">
                        {!! nl2br(!empty($product['description']) ? $product['description'] : 'Sản phẩm đang được cập nhật nội dung mô tả chi tiết.') !!}
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const variantInputs = document.querySelectorAll('input[name="product_variant"]');
    const mainPrice = document.getElementById('main-price');
    const mainImage = document.getElementById('main-product-image');
    const stockLabel = document.getElementById('stock-label');
    const cartBtn = document.getElementById('add-to-cart-link');
    const productId = "{{ $product['id'] }}";

    const checkedVariant = document.querySelector('input[name="product_variant"]:checked');
    if(checkedVariant) {
        updateUI(document.querySelector(`label[for="${checkedVariant.id}"]`));
    } else {
        if(stockLabel) stockLabel.innerText = "Còn hàng";
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
        mainImage.src = label.dataset.image;
        
        const stock = parseInt(label.dataset.stock);
        const variantId = label.dataset.variantId;

        if(stock > 0) {
            stockLabel.innerText = "Còn hàng (" + stock + ")";
            stockLabel.className = "badge bg-success-subtle text-success border-0 px-2 py-1";
            
            cartBtn.href = "{{ rtrim(BASE_URL, '/') }}/cart/add/" + productId + "/" + variantId;
            cartBtn.classList.remove('disabled', 'opacity-50');
            cartBtn.innerText = "THÊM VÀO GIỎ HÀNG";
        } else {
            stockLabel.innerText = "Hết hàng";
            stockLabel.className = "badge bg-danger-subtle text-danger border-0 px-2 py-1";
            
            cartBtn.classList.add('disabled', 'opacity-50');
            cartBtn.href = "javascript:void(0)";
            cartBtn.innerText = "TẠM HẾT HÀNG";
        }
    }
});
</script>

<style>
    .fw-black { font-weight: 900; }
    .leading-loose { line-height: 2; }
    
    .variant-card {
        border-color: #f1f5f9 !important;
        background-color: #ffffff;
        cursor: pointer;
    }
    .variant-card:hover {
        border-color: #cbd5e1 !important;
        background-color: #f8fafc;
    }
    
    .btn-check:checked + .variant-card {
        border-color: #2563eb !important;
        background-color: #eff6ff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }
    .btn-check:checked + .variant-card .name-label { color: #2563eb !important; }
    .btn-check:checked + .variant-card .check-icon { display: block !important; }

    .nav-tabs .nav-link.active { background: transparent !important; color: #2563eb !important; }
    .after-line.active::after {
        content: ''; position: absolute; bottom: -5px; left: 0; width: 100%; height: 3px;
        background: #2563eb; border-radius: 10px;
    }
    @keyframes bounceIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-bounce-in { animation: bounceIn 0.5s ease-out; }
</style>

@include('user.layouts.footer')