@include('admin.layouts.header')

<div class="container mt-4 text-dark">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 animate-slide-down rounded-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold uppercase tracking-tight">
            <i class="bi bi-tags text-primary me-2"></i>Quản lý Danh mục
        </h2>
        <button class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-plus-lg me-1"></i>Thêm danh mục mới
        </button>
    </div>

    <div class="card p-3 mb-4 shadow-sm border-0 rounded-4 bg-white border border-slate-100">
        <form action="{{ rtrim(BASE_URL, '/') }}/admincategory/index" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 shadow-none rounded-end-pill py-2"
                        placeholder="Tìm tên danh mục..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill">TÌM KIẾM</button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ rtrim(BASE_URL, '/') }}/admincategory/index" class="btn btn-outline-secondary w-100 rounded-pill">XÓA LỌC</a>
                </div>
            @endif
        </form>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white border border-slate-50">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark border-0">
                    <tr>
                        <th class="ps-4 py-3" width="100">Ảnh</th>
                        <th>Tên danh mục</th>
                        <th>Mô tả / Thông tin</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $cat)
                    <tr>
                        <td class="ps-4">
                            @php 
                                $urlFix = rtrim(BASE_URL, '/');
                                $urlFix = str_replace('/index.php', '', $urlFix);
                                $imagePath = $urlFix . '/public/uploads/categories/' . ($cat['image'] ?: 'default.jpg');
                            @endphp
                            <img src="{{ $imagePath }}" 
                                 class="rounded shadow-sm border" width="60" height="60" style="object-fit: cover;"
                                 onerror="this.src='https://placehold.co/60x60?text=Cat'">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $cat['name'] }}</div>
                            <div class="extra-small text-muted">ID: #{{ $cat['id'] }}</div>
                        </td>
                        <td>
                            <span class="text-muted small">Phân loại hàng hóa hệ thống</span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                <button class="btn btn-sm btn-white border btn-edit-category"
                                    data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                    data-id="{{ $cat['id'] }}"
                                    data-name="{{ htmlspecialchars($cat['name']) }}"
                                    data-image="{{ $cat['image'] }}">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>
                                <a href="{{ rtrim(BASE_URL, '/') }}/admincategory/destroy/{{ $cat['id'] }}" 
                                   class="btn btn-sm btn-white border" onclick="return confirm('Xác nhận xóa danh mục {{ $cat['name'] }}?')">
                                    <i class="bi bi-trash text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center p-5 text-muted italic">Không tìm thấy kết quả phù hợp.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (isset($totalPages) && $totalPages > 1)
    <nav class="mt-4 mb-5">
        <ul class="pagination justify-content-center gap-2">
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark hover:bg-light' }}" 
                       href="{{ rtrim(BASE_URL, '/') }}/admincategory/index?page={{ $i }}&search={{ urlencode($search ?? '') }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor
        </ul>
    </nav>
    @endif
</div>

@include('admin.category.them')
@include('admin.category.edit')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-category');
        const editForm = document.getElementById('editCategoryForm');
        const imgPreview = document.getElementById('edit_img_preview');

        const jsBaseUrl = '{{ rtrim(BASE_URL, "/") }}'.replace('/index.php', '');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                if (editForm) {
                    editForm.action = '{{ rtrim(BASE_URL, "/") }}/admincategory/update/' + id;
                }
                document.getElementById('edit_name').value = this.dataset.name;
                const img = this.dataset.image;
                if (imgPreview) {
                    imgPreview.src = (img && img !== '') 
                        ? jsBaseUrl + '/public/uploads/categories/' + img 
                        : 'https://placehold.co/120x120?text=No+Image';
                }
            });
        });

        @if(isset($_SESSION['error_type']))
            const modalId = "{{ $_SESSION['error_type'] === 'add' ? '#addCategoryModal' : '#editCategoryModal' }}";
            const targetModal = document.querySelector(modalId);
            if(targetModal) {
                const bootstrapModal = new bootstrap.Modal(targetModal);
                bootstrapModal.show();
            }
        @endif
    });
</script>

<style>
    .extra-small { font-size: 10px; }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

@include('admin.layouts.footer')