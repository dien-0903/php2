@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); 
    $projectPath = str_replace('/public', '', $scriptDir); 
    $projectPath = rtrim($projectPath, '/'); 
    $baseUrl = $protocol . $host . $projectPath;
@endphp

@include('user.layouts.header')

<div class="container py-4 font-inter text-dark">
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

    <div id="js-alert-container"></div>

    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 small">
            <li class="breadcrumb-item"><a href="{{ $baseUrl }}/" class="text-muted text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item active fw-bold text-dark" aria-current="page">Sản phẩm</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <form action="{{ $baseUrl }}/product/index" method="GET">
                    @if(!empty($search)) <input type="hidden" name="search" value="{{ $search }}"> @endif
                    @if(!empty($sort)) <input type="hidden" name="sort" value="{{ $sort }}"> @endif
                    
                    <h5 class="fw-bold mb-3 uppercase tracking-wider small">Danh mục</h5>
                    <ul class="list-unstyled mb-2">
                        <li class="mb-2">
                            <a href="{{ $baseUrl }}/product/index" class="text-decoration-none {{ $currentCat == '' ? 'text-primary fw-bold' : 'text-slate-500' }}">
                                <i class="bi bi-grid-fill me-2"></i> Tất cả
                            </a>
                        </li>
                        @if(isset($categories) && is_array($categories))
                            @foreach($categories as $index => $cat)
                            <li class="mb-2 {{ $index >= 5 ? 'd-none cat-hidden' : '' }}">
                                <a href="?category={{ $cat['id'] }}&search={{ $search ?? '' }}&sort={{ $sort ?? '' }}" 
                                   class="text-decoration-none {{ $currentCat == $cat['id'] ? 'text-primary fw-bold' : 'text-slate-500' }}">
                                    <i class="bi bi-chevron-right me-1 small"></i> {{ $cat['name'] }}
                                </a>
                            </li>
                            @endforeach
                        @endif
                    </ul>

                    @if(isset($categories) && count($categories) > 5)
                        <div class="mb-4 ps-2">
                            <a href="javascript:void(0)" id="btn-toggle-cat" class="text-decoration-none small fw-bold d-flex align-items-center gap-1 text-primary" onclick="toggleCategories()">
                                <span>Xem thêm</span> <i class="bi bi-chevron-down"></i>
                            </a>
                        </div>
                    @else
                        <div class="mb-4"></div>
                    @endif

                    <input type="hidden" name="category" value="{{ $currentCat }}">

                    <h5 class="fw-bold mb-3 uppercase tracking-wider small">Khoảng giá</h5>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <input type="number" name="min_price" value="{{ $_GET['min_price'] ?? '' }}" 
                               class="form-control form-control-sm rounded-3 bg-light border-0" placeholder="Từ" min="0">
                        <span>-</span>
                        <input type="number" name="max_price" value="{{ $_GET['max_price'] ?? '' }}" 
                               class="form-control form-control-sm rounded-3 bg-light border-0" placeholder="Đến" min="0">
                    </div>
                    <button type="submit" class="btn btn-dark btn-sm w-100 rounded-pill fw-bold">LỌC GIÁ</button>
                    
                    @if(isset($_GET['min_price']) || isset($_GET['max_price']))
                        <a href="{{ $baseUrl }}/product/index" class="btn btn-light btn-sm w-100 rounded-pill fw-bold mt-2 text-muted">Xóa lọc</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 bg-white p-3 rounded-4 shadow-sm border border-slate-50">
                <div>
                    <h1 class="h4 font-black text-slate-900 tracking-tighter uppercase mb-0">Danh sách sản phẩm</h1>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ $baseUrl }}/product/index" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="category" value="{{ $currentCat }}">
                        <input type="hidden" name="min_price" value="{{ $_GET['min_price'] ?? '' }}">
                        <input type="hidden" name="max_price" value="{{ $_GET['max_price'] ?? '' }}">
                        <input type="hidden" name="search" value="{{ $_GET['search'] ?? '' }}">
                        <select name="sort" class="form-select border-0 bg-light rounded-pill shadow-none fw-bold small text-secondary" style="width: 160px;" onchange="this.form.submit()">
                            <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>Giá: Thấp -> Cao</option>
                            <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>Giá: Cao -> Thấp</option>
                            <option value="name_asc" {{ $sort == 'name_asc' ? 'selected' : '' }}>Tên: A -> Z</option>
                        </select>
                    </form>
                    <form action="{{ $baseUrl }}/product/index" method="GET">
                         <div class="input-group bg-light rounded-pill border-0" style="width: 200px;">
                             <input type="text" name="search" value="{{ $_GET['search'] ?? '' }}" class="form-control border-0 bg-transparent shadow-none py-1 small ps-3" placeholder="Tìm kiếm...">
                             <button type="submit" class="btn btn-link text-dark shadow-none border-0"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3">
                @if(isset($products) && !empty($products))
                    @foreach($products as $item)
                        @php 
                            $inWishlist = in_array($item['id'], $wishlist ?? []);
                            $isOutOfStock = isset($item['stock']) && $item['stock'] <= 0; 
                        @endphp
                        <div class="col-6 col-md-4">
                            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden hover:shadow-xl transition-all border border-slate-50 group bg-white position-relative">
                                
                                <div class="position-absolute top-0 end-0 m-2 d-flex flex-column gap-2" style="z-index: 10;">
                                    @if($inWishlist)
                                        <a href="{{ $baseUrl }}/wishlist/remove/{{ $item['id'] }}" 
                                           class="btn btn-white bg-white rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center text-danger hover:bg-light transition-all btn-ajax-action" 
                                           data-type="wishlist" data-id="{{ $item['id'] }}" data-action="remove"
                                           style="width: 32px; height: 32px;" title="Bỏ yêu thích"><i class="bi bi-heart-fill small"></i></a>
                                    @else
                                        <a href="{{ $baseUrl }}/wishlist/add/{{ $item['id'] }}" 
                                           class="btn btn-white bg-white rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center text-secondary hover:text-danger hover:bg-white transition-all btn-ajax-action" 
                                           data-type="wishlist" data-id="{{ $item['id'] }}" data-action="add"
                                           style="width: 32px; height: 32px;" title="Thêm yêu thích"><i class="bi bi-heart small"></i></a>
                                    @endif
                                </div>

                                <a href="{{ $baseUrl }}/product/show/{{ $item['id'] }}" class="text-decoration-none d-block">
                                    <div class="bg-white p-3 d-flex align-items-center justify-content-center relative overflow-hidden" style="height: 220px;">
                                        @php $APP_URL = 'http://localhost/PHP2'; $imagePath = $APP_URL . '/public/uploads/products/' . ($item['image'] ?: 'default.jpg'); @endphp
                                        
                                        <img src="{{ $imagePath }}" 
                                             class="img-fluid transition-transform group-hover:scale-105" 
                                             style="max-height: 100%; width: auto; object-fit: contain; {{ $isOutOfStock ? 'filter: grayscale(100%); opacity: 0.6;' : '' }}"
                                             onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                                        
                                        @if($isOutOfStock)
                                            <div class="position-absolute top-50 start-50 translate-middle bg-secondary text-white px-3 py-1 rounded-pill small fw-bold shadow-sm" style="z-index: 5; white-space: nowrap;">
                                                HẾT HÀNG
                                            </div>
                                        @endif
                                    </div>
                                </a>

                                <div class="card-body p-3 d-flex flex-column text-dark">
                                    <div class="mb-auto">
                                        <a href="{{ $baseUrl }}/product/show/{{ $item['id'] }}" class="text-decoration-none text-dark">
                                            <span class="text-[10px] text-blue-500 font-bold uppercase tracking-widest" style="font-size: 10px;">{{ $item['category_name'] ?? 'Thiết bị' }}</span>
                                            <h6 class="card-title fw-bold text-dark mt-1 mb-2 line-clamp-2" style="font-size: 0.95rem;">{{ $item['name'] }}</h6>
                                        </a>
                                    </div>
                                    <div class="mt-1 d-flex justify-content-between align-items-end">
                                        <span class="text-danger font-black fs-5 mb-0 fw-bold">{{ number_format($item['price'], 0, ',', '.') }}đ</span>
                                        
                                        @if($isOutOfStock)
                                            <button type="button" 
                                                class="btn btn-secondary rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm" 
                                                style="width: 35px; height: 35px; cursor: not-allowed; opacity: 0.5; background-color: #e2e8f0; border: none;" 
                                                title="Sản phẩm đã hết hàng" disabled>
                                                <i class="bi bi-x-lg text-secondary"></i>
                                            </button>
                                        @else
                                            <a href="{{ $baseUrl }}/cart/add/{{ $item['id'] }}" 
                                               class="btn btn-primary rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm hover:bg-blue-700 transition-all btn-ajax-action" 
                                               data-type="cart" style="width: 35px; height: 35px;" title="Thêm vào giỏ hàng">
                                                <i class="bi bi-cart-plus text-white"></i>
                                            </a>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-5 shadow-sm border border-dashed border-slate-200 text-dark">
                            <i class="bi bi-search fs-1 text-slate-200 d-block mb-3"></i>
                            <h5 class="fw-bold">Không tìm thấy sản phẩm nào!</h5>
                            <a href="{{ $baseUrl }}/product/index" class="btn btn-dark rounded-pill mt-3 px-4">Xóa bộ lọc</a>
                        </div>
                    </div>
                @endif
            </div>

            @if(isset($totalPages) && $totalPages > 1)
                <div class="mt-5 d-flex justify-content-center">
                    <nav>
                        <ul class="pagination gap-2">
                            @if($currentPage > 1)
                            <li class="page-item"><a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold bg-white text-dark" href="{{ $baseUrl }}/product/index?page={{ $currentPage - 1 }}&search={{ $search ?? '' }}&sort={{ $sort ?? '' }}&category={{ $currentCat ?? '' }}&min_price={{ $minPrice ?? '' }}&max_price={{ $maxPrice ?? '' }}"><i class="bi bi-chevron-left"></i></a></li>
                            @endif
                            @for($i = 1; $i <= $totalPages; $i++)
                                <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}"><a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white' : 'bg-white text-dark' }}" href="{{ $baseUrl }}/product/index?page={{ $i }}&search={{ $search ?? '' }}&sort={{ $sort ?? '' }}&category={{ $currentCat ?? '' }}&min_price={{ $minPrice ?? '' }}&max_price={{ $maxPrice ?? '' }}">{{ $i }}</a></li>
                            @endfor
                            @if($currentPage < $totalPages)
                            <li class="page-item"><a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold bg-white text-dark" href="{{ $baseUrl }}/product/index?page={{ $currentPage + 1 }}&search={{ $search ?? '' }}&sort={{ $sort ?? '' }}&category={{ $currentCat ?? '' }}&min_price={{ $minPrice ?? '' }}&max_price={{ $maxPrice ?? '' }}"><i class="bi bi-chevron-right"></i></a></li>
                            @endif
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>


<script>
    function toggleCategories() {
        const items = document.querySelectorAll('.cat-hidden');
        const btn = document.getElementById('btn-toggle-cat');
        const span = btn.querySelector('span');
        const icon = btn.querySelector('i');
        let isHidden = items.length > 0 && items[0].classList.contains('d-none');
        items.forEach(item => isHidden ? item.classList.remove('d-none') : item.classList.add('d-none'));
        span.innerText = isHidden ? 'Thu gọn' : 'Xem thêm';
        icon.className = isHidden ? 'bi bi-chevron-up' : 'bi bi-chevron-down';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const alertContainer = document.getElementById('js-alert-container');

        document.querySelectorAll('.btn-ajax-action').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.href;
                const type = this.dataset.type; 
                const currentBtn = this;
                currentBtn.style.opacity = '0.7';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.json())
                .then(data => {
                    currentBtn.style.opacity = '1';
                    if(data.success) {
                        
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4 animate-bounce-in">
                                <div class="d-flex align-items-center text-success fw-bold">
                                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                                    <span>${data.message}</span>
                                </div>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        alertContainer.innerHTML = alertHtml;
                    
                        if(type === 'wishlist') {
                            const icon = currentBtn.querySelector('i');
                            const action = currentBtn.dataset.action;
                            const id = currentBtn.dataset.id;
                            if(action === 'add') {
                                icon.classList.replace('bi-heart', 'bi-heart-fill');
                                currentBtn.classList.replace('text-secondary', 'text-danger');
                                currentBtn.dataset.action = 'remove';
                                currentBtn.title = 'Bỏ yêu thích';
                                currentBtn.href = "{{ rtrim($baseUrl, '/') }}/wishlist/remove/" + id;
                            } else {
                                icon.classList.replace('bi-heart-fill', 'bi-heart');
                                currentBtn.classList.replace('text-danger', 'text-secondary');
                                currentBtn.dataset.action = 'add';
                                currentBtn.title = 'Thêm yêu thích';
                                currentBtn.href = "{{ rtrim($baseUrl, '/') }}/wishlist/add/" + id;
                            }
                        }
                    } else {
                        const errorHtml = `
                            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-4 animate-bounce-in">
                                <div class="d-flex align-items-center text-danger fw-bold">
                                    <i class="bi bi-exclamation-circle-fill fs-4 me-3"></i>
                                    <span>${data.message || 'Có lỗi xảy ra!'}</span>
                                </div>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        alertContainer.innerHTML = errorHtml;
                    }
                })
                .catch(err => { 
                    console.error(err); 
                    window.location.href = url;
                });
            });
        });
    });
</script>

@include('user.layouts.footer')