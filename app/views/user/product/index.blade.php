@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
@endphp

@include('user.layouts.header')

@if(isset($_SESSION['success']))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 py-3 px-4 mb-5 animate-bounce-in">
        <div class="d-flex align-items-center text-success fw-bold">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <span>{{ $_SESSION['success'] }}</span>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert"></button>
    </div>
    @php unset($_SESSION['success']) @endphp
@endif

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-4">
    <div>
        <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase mb-1">
            Thiết bị công nghệ
        </h1>
        <p class="text-slate-500 mb-0">Khám phá bộ sưu tập mới nhất (8 sản phẩm/trang)</p>
    </div>
    
    <form action="{{ rtrim(BASE_URL, '/') }}/product/index" method="GET" class="d-flex gap-2">
        <div class="input-group bg-white rounded-pill shadow-sm px-3 border border-slate-200">
            <span class="input-group-text bg-transparent border-0 text-slate-400">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" name="search" value="{{ $_GET['search'] ?? '' }}" 
                   class="form-control border-0 shadow-none py-2" placeholder="Tìm tên sản phẩm...">
            <button type="submit" class="btn btn-primary rounded-pill px-4 my-1 fw-bold shadow-none text-white">TÌM</button>
        </div>
    </form>
</div>

<div class="row g-4">
    @if(isset($products) && !empty($products))
        @foreach($products as $item)
            <div class="col-sm-6 col-lg-4 col-xl-3">
                <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden hover:shadow-xl transition-all border border-slate-50 group bg-white">
                    
                    <a href="{{ rtrim(BASE_URL, '/') }}/product/show/{{ $item['id'] }}" class="text-decoration-none">
                        <div class="bg-slate-50 p-5 d-flex align-items-center justify-content-center relative overflow-hidden" style="height: 220px;">
                            <img src="{{ rtrim(BASE_URL, '/') }}/public/uploads/products/{{ $item['image'] ?? 'default.jpg' }}" 
                                 class="mw-100 mh-100 object-contain transition-transform duration-500 group-hover:scale-110" 
                                 style="max-height: 150px;" 
                                 alt="{{ $item['name'] }}"
                                 onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                            
                            <div class="absolute top-3 left-3">
                                 <span class="badge bg-primary rounded-pill px-2 py-1 small fw-bold" style="font-size: 10px;">CHÍNH HÃNG</span>
                            </div>
                        </div>

                        <div class="card-body p-4 d-flex flex-column text-dark">
                            <div class="mb-auto">
                                <span class="text-[10px] text-blue-500 font-bold uppercase tracking-widest">
                                    {{ $item['category_name'] ?? 'Thiết bị' }}
                                </span>
                                <h5 class="card-title fw-bold text-dark mt-1 mb-2 line-clamp-2" style="min-height: 2.8rem; font-size: 1rem;">
                                    {{ $item['name'] }}
                                </h5>
                            </div>
                            
                            <div class="mt-1">
                                <p class="text-rose-600 font-black fs-5 mb-0">
                                    {{ number_format($item['price'], 0, ',', '.') }}đ
                                </p>
                            </div>
                        </div>
                    </a>

                    <div class="card-body pt-0 px-4 pb-4">
                        <div class="d-grid">
                            <a href="{{ rtrim(BASE_URL, '/') }}/cart/add/{{ $item['id'] }}" 
                               class="btn btn-primary rounded-pill fw-bold text-[11px] py-2 shadow-sm text-white transition-all hover:bg-blue-700">
                                <i class="bi bi-cart-plus me-1"></i> THÊM VÀO GIỎ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="col-12 text-center py-5">
            <div class="bg-white p-5 rounded-5 shadow-sm border border-dashed border-slate-200 text-dark">
                <i class="bi bi-inbox fs-1 text-slate-200 d-block mb-3" style="font-size: 4rem !important;"></i>
                <h5 class="fw-bold">Không tìm thấy sản phẩm nào!</h5>
                <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-primary rounded-pill mt-3 text-white">XEM TẤT CẢ</a>
            </div>
        </div>
    @endif
</div>

@if(isset($totalPages) && $totalPages > 1)
    <div class="mt-5 d-flex justify-content-center">
        <nav>
            <ul class="pagination gap-2">
                @for($i = 1; $i <= $totalPages; $i++)
                    <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                        <a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white' : 'bg-white text-dark' }}" 
                           href="{{ rtrim(BASE_URL, '/') }}/product/index?page={{ $i }}&search={{ $_GET['search'] ?? '' }}">
                            {{ $i }}
                        </a>
                    </li>
                @endfor
            </ul>
        </nav>
    </div>
@endif

<style>
    @keyframes bounceIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-bounce-in { animation: bounceIn 0.5s ease-out; }
    .card a.text-decoration-none:hover h5 { color: #2563eb !important; transition: color 0.3s ease; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

@include('user.layouts.footer')