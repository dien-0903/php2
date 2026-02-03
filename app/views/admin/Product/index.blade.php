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
            <i class="bi bi-box-seam text-primary me-2"></i>Quản lý Sản phẩm
        </h2>
        <!-- Added type="button" to prevent form submission if inside one -->
        <button type="button" class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm mới
        </button>
    </div>

    <div class="card p-3 mb-4 shadow-sm border-0 rounded-4 bg-white border border-slate-100">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminproduct/index" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 shadow-none rounded-end-pill py-2"
                        placeholder="Tìm tên sản phẩm..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill text-white">TÌM KIẾM</button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ rtrim(BASE_URL, '/') }}/adminproduct/index" class="btn btn-outline-secondary w-100 rounded-pill">XÓA LỌC</a>
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
                        <th>Thông tin sản phẩm</th>
                        <th>Giá bán</th>
                        <th>Danh mục</th>
                        <th>Tồn kho</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $p)
                    <tr>
                        <td class="ps-4">
                            @php        
                                $APP_URL = 'http://localhost/PHP2';
                                $imagePath = $APP_URL . '/public/uploads/products/' . ($p['image'] ?: 'default.jpg');
                            @endphp
                            <img src="{{ $imagePath }}" 
                                 class="rounded shadow-sm border" width="60" height="60" style="object-fit: cover;"
                                 onerror="this.src='https://placehold.co/100x100?text=SP'">
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p['name'] }}</div>
                            <div class="extra-small text-muted">ID: #{{ $p['id'] }} | Hãng: {{ $p['brand_name'] ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="text-danger fw-bold">{{ number_format($p['price']) }}đ</span>
                        </td>
                        <td>
                            <span class="badge bg-info-subtle text-info border-0 px-3 rounded-pill fw-bold">{{ $p['category_name'] ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <!-- Stock Column Logic Updated -->
                            @php
                                $stock = $p['stock'] ?? 0;
                                if ($stock > 10) {
                                    // Lớn hơn 10: Xanh lá
                                    $badgeClass = 'bg-success-subtle text-success';
                                } elseif ($stock > 0) {
                                    // Từ 1 đến 10: Cam (Cảnh báo sắp hết)
                                    $badgeClass = 'bg-warning-subtle text-warning';
                                } else {
                                    // Nhỏ hơn 1 (<= 0): Đỏ
                                    $badgeClass = 'bg-danger-subtle text-danger';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }} border-0 px-3 rounded-pill fw-bold">
                                {{ $stock > 0 ? ($stock . ' SP') : 'Hết hàng' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                <a href="{{ rtrim(BASE_URL, '/') }}/adminvariant/index/{{ $p['id'] }}" 
                                   class="btn btn-sm btn-white border border-end-0 text-primary" 
                                   title="Cấu hình Màu sắc & Kích thước">
                                    <i class="bi bi-gear-wide-connected"></i>
                                </a>

                                {{-- Edit Button --}}
                                <button type="button" class="btn btn-sm btn-white border btn-edit-product"
                                    data-bs-toggle="modal" data-bs-target="#editProductModal"
                                    data-id="{{ $p['id'] }}"
                                    data-name="{{ htmlspecialchars($p['name']) }}"
                                    data-price="{{ $p['price'] }}"
                                    data-category="{{ $p['category_id'] }}"
                                    data-brand="{{ $p['brand_id'] }}"
                                    data-description="{{ htmlspecialchars($p['description'] ?? '') }}"
                                    data-stock="{{ $p['stock'] ?? 0 }}"
                                    data-image="{{ $p['image'] }}">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>

                                <a href="{{ rtrim(BASE_URL, '/') }}/adminproduct/destroy/{{ $p['id'] }}" 
                                   class="btn btn-sm btn-white border text-danger" 
                                   onclick="return confirm('Xác nhận xóa sản phẩm {{ $p['name'] }}?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-5 text-muted italic">Không tìm thấy sản phẩm nào phù hợp.</td>
                    </tr>
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
                       href="{{ rtrim(BASE_URL, '/') }}/adminproduct/index?page={{ $i }}&search={{ urlencode($search ?? '') }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor
        </ul>
    </nav>
    @endif
</div>

@include('admin.product.them')
@include('admin.product.edit')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-product');
        const editForm = document.getElementById('editProductForm');
        const imgPreview = document.getElementById('edit_img_preview');

        const jsBaseUrl = '{{ rtrim(BASE_URL, "/") }}'.replace('/index.php', '');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                if (editForm) {
                    editForm.action = '{{ rtrim(BASE_URL, "/") }}/adminproduct/update/' + id;
                }

                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_price').value = this.dataset.price;
                document.getElementById('edit_category_id').value = this.dataset.category;
                document.getElementById('edit_brand_id').value = this.dataset.brand;
                
                // Populate Stock and Description
                const stockInput = document.getElementById('edit_stock');
                if(stockInput) stockInput.value = this.dataset.stock;

                const descInput = document.getElementById('edit_description');
                if(descInput) descInput.value = this.dataset.description;

                const img = this.dataset.image;
                if (imgPreview) {
                    imgPreview.src = (img && img !== '') 
                        ? jsBaseUrl + '/public/uploads/products/' + img 
                        : 'https://placehold.co/120x120?text=No+Image';
                }
            });
        });

        @if(isset($_SESSION['error_type']))
            const modalId = "{{ $_SESSION['error_type'] === 'add' ? '#addProductModal' : '#editProductModal' }}";
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
    .btn-white:hover { background-color: #f8fafc; }
</style>

@include('admin.layouts.footer')