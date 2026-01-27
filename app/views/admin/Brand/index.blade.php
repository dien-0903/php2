@include('admin.layouts.header')

<div class="container mt-4 text-dark">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center rounded-4 animate-slide-down">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold uppercase tracking-tight text-dark">
            <i class="bi bi-award text-primary me-2"></i>Quản lý Thương hiệu
        </h2>
        <button class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold" 
                data-bs-toggle="modal" data-bs-target="#addBrandModal">
            <i class="bi bi-plus-lg me-1"></i>Thêm thương hiệu mới
        </button>
    </div>

    <div class="card p-3 mb-4 shadow-sm border-0 rounded-4 bg-white border border-slate-100">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminbrand/index" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 shadow-none rounded-end-pill py-2"
                        placeholder="Tìm tên thương hiệu..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill">TÌM KIẾM</button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ rtrim(BASE_URL, '/') }}/adminbrand/index" class="btn btn-outline-secondary w-100 rounded-pill">XÓA LỌC</a>
                </div>
            @endif
        </form>
    </div>

    <div class="row g-4 mb-5">
        @forelse ($brands as $b)
        <div class="col-sm-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden group bg-white border border-slate-100">
                
                <div class="bg-light p-4 d-flex flex-column align-items-center justify-content-center aspect-square position-relative" style="height: 200px;">
                    @php 
                        $urlFix = rtrim(BASE_URL, '/');
                        $urlFix = str_replace('/index.php', '', $urlFix);
                        $imagePath = $urlFix . '/public/uploads/brands/' . ($b['image'] ?: 'default.jpg');
                    @endphp

                    <img src="{{ $imagePath }}" 
                         class="img-fluid object-fit-contain transition-transform group-hover:scale-110" 
                         style="max-height: 100px;"
                         alt="{{ $b['name'] }}"
                         onerror="this.src='https://placehold.co/150x150?text=No+Image'">
                </div>
                
                <div class="card-body p-4 text-center d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-1">{{ $b['name'] }}</h5>
                    <p class="text-muted small mb-4 line-clamp-2" style="min-height: 2.5rem;">{{ $b['description'] ?: 'Chưa có mô tả.' }}</p>
                    
                    <div class="mt-auto d-flex gap-2">
                        <button class="btn btn-sm btn-outline-warning flex-grow-1 rounded-pill fw-bold btn-edit-brand"
                            data-bs-toggle="modal" data-bs-target="#editBrandModal"
                            data-id="{{ $b['id'] }}"
                            data-name="{{ htmlspecialchars($b['name']) }}"
                            data-description="{{ htmlspecialchars($b['description'] ?? '') }}"
                            data-image="{{ $b['image'] }}">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </button>
                        <a href="{{ rtrim(BASE_URL, '/') }}/adminbrand/destroy/{{ $b['id'] }}" 
                           class="btn btn-sm btn-outline-danger px-3 rounded-pill" 
                           onclick="return confirm('Xác nhận xóa thương hiệu {{ $b['name'] }}?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-4 border border-dashed border-slate-200">
                <i class="bi bi-search fs-1 text-slate-200"></i>
                <p class="text-muted mt-3">Không tìm thấy thương hiệu nào phù hợp.</p>
            </div>
        </div>
        @endforelse
    </div>

    @if (isset($totalPages) && $totalPages > 1)
    <nav class="mt-4 mb-5">
        <ul class="pagination justify-content-center gap-2">
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark hover:bg-light' }}" 
                       href="{{ rtrim(BASE_URL, '/') }}/adminbrand/index?page={{ $i }}&search={{ urlencode($search ?? '') }}">{{ $i }}</a>
                </li>
            @endfor
        </ul>
    </nav>
    @endif
</div>

@include('admin.brand.them')
@include('admin.brand.edit')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-brand');
        const editForm = document.getElementById('editBrandForm');
        const imgPreview = document.getElementById('edit_brand_img_preview');

        const jsBaseUrl = '{{ rtrim(BASE_URL, "/") }}'.replace('/index.php', '');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                if(editForm) editForm.action = '{{ rtrim(BASE_URL, "/") }}/adminbrand/update/' + id;
                
                document.getElementById('edit_brand_name').value = this.dataset.name;
                document.getElementById('edit_brand_description').value = this.dataset.description;

                const img = this.dataset.image;
                if (imgPreview) {
                    imgPreview.src = (img && img !== '') 
                        ? jsBaseUrl + '/public/uploads/brands/' + img 
                        : 'https://placehold.co/150x150?text=No+Image';
                }
            });
        });

        @if(isset($_SESSION['error_type']))
            const modalId = "{{ $_SESSION['error_type'] === 'add' ? '#addBrandModal' : '#editBrandModal' }}";
            const targetModal = document.querySelector(modalId);
            if(targetModal) {
                const bootstrapModal = new bootstrap.Modal(targetModal);
                bootstrapModal.show();
            }
        @endif
    });
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

@include('admin.layouts.footer')