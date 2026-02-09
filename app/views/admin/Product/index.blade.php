@include('admin.layouts.header')

<div class="container mt-4 text-dark animate-slide-down">
    <!-- Tiêu đề trang và Nút thêm mới -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold uppercase tracking-tight text-slate-800">
            <i class="bi bi-box-seam text-primary me-2"></i> QUẢN LÝ SẢN PHẨM
        </h2>
        <button type="button" class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold transition-all hover-lift" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg me-1"></i> THÊM SẢN PHẨM MỚI
        </button>
    </div>

    <!-- Thanh tìm kiếm tối giản -->
    <div class="card p-3 mb-4 shadow-sm border-0 rounded-4 bg-white border border-slate-100">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminproduct/index" method="GET" class="row g-2 justify-content-center">
            <div class="col-md-7">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted rounded-start-pill ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 shadow-none rounded-end-pill py-2.5"
                        placeholder="Nhập tên sản phẩm cần tìm..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill py-2.5 shadow-sm">TÌM KIẾM</button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ rtrim(BASE_URL, '/') }}/adminproduct/index" class="btn btn-outline-secondary w-100 rounded-pill py-2.5">XÓA LỌC</a>
                </div>
            @endif
        </form>
    </div>

    <!-- Bảng danh sách sản phẩm -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
                <thead class="table-dark border-0">
                    <tr class="small text-uppercase">
                        <th class="ps-4 py-3" width="100">Ảnh</th>
                        <th>Thông tin sản phẩm</th>
                        <th class="text-center">Giá bán</th>
                        <th class="text-center">Danh mục</th>
                        <th class="text-center">Tồn kho</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $p)
                    <tr>
                        <td class="ps-4">
                            @php        
                                // URL cố định cho hình ảnh theo yêu cầu của bạn
                                $APP_URL = 'http://localhost/PHP2';
                                $imagePath = $APP_URL . '/public/uploads/products/' . ($p['image'] ?: 'default.jpg');
                            @endphp
                            <div class="bg-light rounded p-1 border shadow-sm d-flex align-items-center justify-content-center overflow-hidden" style="width: 65px; height: 65px;">
                                <img src="{{ $imagePath }}" class="w-100 h-100 object-fit-contain rounded" 
                                     onerror="this.src='https://placehold.co/100x100?text=SP'">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $p['name'] }}</div>
                            <div class="extra-small text-muted">ID: #{{ $p['id'] }} | Hãng: <span class="fw-bold">{{ $p['brand_name'] ?? 'N/A' }}</span></div>
                        </td>
                        <td class="text-center">
                            <span class="text-danger fw-black fs-6">{{ number_format($p['price'], 0, ',', '.') }}đ</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info-subtle text-info border-0 px-3 py-2 rounded-pill fw-bold">{{ $p['category_name'] ?? 'N/A' }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $stock = (int)($p['stock'] ?? 0);
                                if ($stock > 10) {
                                    $badgeClass = 'bg-success-subtle text-success';
                                    $text = $stock . ' SP';
                                } elseif ($stock > 0) {
                                    $badgeClass = 'bg-warning-subtle text-warning';
                                    $text = $stock . ' SP';
                                } else {
                                    $badgeClass = 'bg-danger-subtle text-danger';
                                    $text = 'Hết hàng';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }} border-0 px-3 py-2 rounded-pill fw-bold">
                                {{ $text }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden border">
                                <!-- Quản lý biến thể -->
                                <a href="{{ rtrim(BASE_URL, '/') }}/adminvariant/index/{{ $p['id'] }}" 
                                   class="btn btn-sm btn-white px-2.5 text-primary" title="Cấu hình Màu & Size">
                                    <i class="bi bi-gear-fill"></i>
                                </a>
                                
                                <!-- Sửa sản phẩm (Nạp dữ liệu vào Modal qua data-attribute) -->
                                <button type="button" class="btn btn-sm btn-white px-2.5 btn-edit-product"
                                    data-bs-toggle="modal" data-bs-target="#editProductModal"
                                    data-id="{{ $p['id'] }}"
                                    data-name="{{ htmlspecialchars($p['name']) }}"
                                    data-price="{{ $p['price'] }}"
                                    data-category="{{ $p['category_id'] }}"
                                    data-brand="{{ $p['brand_id'] }}"
                                    data-stock="{{ $p['stock'] }}"
                                    data-description="{{ htmlspecialchars($p['description'] ?? '') }}"
                                    data-image="{{ $p['image'] }}"
                                    data-gallery='{{ json_encode($p['gallery'] ?? []) }}'>
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>

                                <!-- Xóa sản phẩm -->
                                <a href="{{ rtrim(BASE_URL, '/') }}/adminproduct/destroy/{{ $p['id'] }}" 
                                   class="btn btn-sm btn-white px-2.5 text-danger" 
                                   onclick="return confirm('Toàn bộ biến thể và hình ảnh của sản phẩm này sẽ bị xóa. Bạn chắc chắn chứ?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-5 text-muted italic border-0">
                            <div class="display-6 opacity-25 mb-2"><i class="bi bi-inbox"></i></div>
                            Không tìm thấy sản phẩm nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Phân trang -->
    @if (isset($totalPages) && $totalPages > 1)
    <nav class="mt-4 mb-5">
        <ul class="pagination justify-content-center gap-2">
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark shadow-hover' }}" 
                       href="?act=adminproduct/index&page={{ $i }}&search={{ urlencode($search ?? '') }}">{{ $i }}</a>
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
        const galleryContainer = document.getElementById('edit_gallery_container');
        
        // URL cơ sở cho hình ảnh
        const jsBaseUrl = 'http://localhost/PHP2';

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                
                // 1. Cập nhật action cho form sửa để trỏ đúng ID sản phẩm
                if (editForm) {
                    editForm.action = '{{ rtrim(BASE_URL, "/") }}/adminproduct/update/' + id;
                }

                // 2. Điền thông tin văn bản vào các ô input trong Modal
                document.getElementById('edit_name').value = this.dataset.name;
                document.getElementById('edit_price').value = this.dataset.price;
                document.getElementById('edit_stock').value = this.dataset.stock;
                document.getElementById('edit_category_id').value = this.dataset.category;
                document.getElementById('edit_brand_id').value = this.dataset.brand;
                document.getElementById('edit_description').value = this.dataset.description;

                // 3. Hiển thị ảnh đại diện chính hiện tại
                const mainImg = this.dataset.image;
                if (imgPreview) {
                    imgPreview.src = (mainImg && mainImg !== '') 
                        ? jsBaseUrl + '/public/uploads/products/' + mainImg 
                        : 'https://placehold.co/300x300?text=No+Image';
                }

                // 4. RENDER THƯ VIỆN ẢNH (GALLERY) VÀ NÚT XÓA AJAX
                if (galleryContainer) {
                    galleryContainer.innerHTML = ''; // Làm sạch khu vực hiển thị cũ
                    
                    try {
                        const gallery = JSON.parse(this.dataset.gallery || '[]');
                        
                        if (gallery.length > 0) {
                            gallery.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'col-3 mb-2';
                                div.innerHTML = `
                                    <div class="gallery-item-wrapper border rounded p-1 position-relative bg-white shadow-sm overflow-hidden" style="height: 80px;">
                                        <img src="${jsBaseUrl}/public/uploads/products/${item.image}" 
                                             class="w-100 h-100 object-fit-cover rounded" 
                                             onerror="this.src='https://placehold.co/100x100?text=Error'">
                                        
                                        <!-- Nút xóa gọi hàm AJAX trong edit.blade.php -->
                                        <button type="button" 
                                                class="btn btn-danger btn-sm btn-delete-gallery-img" 
                                                style="position: absolute; top: -2px; right: -2px; border-radius: 50%; padding: 0 5px;"
                                                onclick="deleteGalleryImageAjax(${item.id}, this)">
                                            <i class="bi bi-x" style="font-size: 12px;"></i>
                                        </button>
                                    </div>
                                `;
                                galleryContainer.appendChild(div);
                            });
                        } else {
                            galleryContainer.innerHTML = '<div class="col-12 text-center py-3 text-muted small italic">Thư viện ảnh trống.</div>';
                        }
                    } catch (e) {
                        console.error("Lỗi xử lý dữ liệu Gallery:", e);
                    }
                }
            });
        });

        // Tự động mở Modal nếu có lỗi từ Server (Xử lý Flash Session)
        @if(isset($_SESSION['error_type']))
            const modalId = "{{ $_SESSION['error_type'] === 'add' ? '#addProductModal' : '#editProductModal' }}";
            const targetModal = document.querySelector(modalId);
            if(targetModal) {
                const bootstrapModal = new bootstrap.Modal(targetModal);
                bootstrapModal.show();
            }
        @endif
    });

    /**
     * Xem trước ảnh đại diện chính khi Admin chọn file mới
     */
    function previewMainImage(input, previewId = 'edit_img_preview') {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => preview.src = e.target.result;
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    /* Kiểu dáng giao diện nâng cao */
    .btn-white { background: #fff; border-color: #dee2e6; }
    .btn-white:hover { background: #f8f9fa; color: #000; }
    .object-fit-contain { object-fit: contain; }
    .object-fit-cover { object-fit: cover; }
    .fw-black { font-weight: 900; }
    
    /* Màu sắc các Badge trạng thái */
    .bg-info-subtle { background-color: #e0f2fe !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; }
    
    .extra-small { font-size: 11px; }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .shadow-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
    .hover-lift:hover { transform: translateY(-3px); }
    .transition-all { transition: all 0.3s ease; }
    
    /* Hiệu ứng trượt xuống cho các thông báo và nội dung */
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { 
        from { transform: translateY(-10px); opacity: 0; } 
        to { transform: translateY(0); opacity: 1; } 
    }
</style>

@include('admin.layouts.footer')