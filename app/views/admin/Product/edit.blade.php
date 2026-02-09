<!-- Modal Chỉnh sửa Sản phẩm -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Form cập nhật sản phẩm hỗ trợ gửi dữ liệu đa phương thức (enctype) -->
        <form id="editProductForm" action="" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0">
            
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>CẬP NHẬT SẢN PHẨM & HÌNH ẢNH
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-dark text-start">
                <!-- KHỐI THÔNG TIN CƠ BẢN -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">TÊN SẢN PHẨM</label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-3 shadow-none border-slate-200" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">GIÁ BÁN (VNĐ)</label>
                        <input type="number" name="price" id="edit_price" class="form-control rounded-3 shadow-none border-slate-200" min="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">TỒN KHO</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control rounded-3 shadow-none border-slate-200" min="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">DANH MỤC</label>
                        <select name="category_id" id="edit_category_id" class="form-select rounded-3 shadow-none border-slate-200" required>
                            @if(!empty($all_categories))
                                @foreach ($all_categories as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">THƯƠNG HIỆU</label>
                        <select name="brand_id" id="edit_brand_id" class="form-select rounded-3 shadow-none border-slate-200" required>
                            @if(!empty($all_brands))
                                @foreach ($all_brands as $b)
                                    <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-secondary">MÔ TẢ CHI TIẾT</label>
                    <textarea name="description" id="edit_description" class="form-control rounded-3 shadow-none border-slate-200" rows="3"></textarea>
                </div>

                <hr class="opacity-10 mb-4">

                <!-- KHỐI QUẢN LÝ HÌNH ẢNH (CHÍNH & GALLERY) -->
                <div class="row g-4">
                    <!-- PHẦN ẢNH ĐẠI DIỆN CHÍNH -->
                    <div class="col-md-5 border-end">
                        <label class="form-label fw-bold small text-secondary text-uppercase mb-2">Ảnh đại diện chính</label>
                        <div class="bg-light rounded-4 p-2 border mb-3 text-center d-flex align-items-center justify-content-center shadow-inner overflow-hidden" style="height: 220px;">
                            <img id="edit_img_preview" src="" 
                                 class="img-fluid rounded-3" 
                                 style="max-height: 100%; object-fit: contain;" 
                                 onerror="this.src='https://placehold.co/300x300?text=No+Image'">
                        </div>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-camera-fill"></i></span>
                            <input type="file" name="image" class="form-control rounded-end-3 shadow-none border-slate-200" accept="image/*" onchange="previewMainImage(this)">
                        </div>
                    </div>

                    <!-- PHẦN THƯ VIỆN ẢNH PHỤ (GALLERY) -->
                    <div class="col-md-7">
                        <label class="form-label fw-bold small text-secondary text-uppercase mb-2">Thư viện ảnh (Gallery)</label>
                        
                        <!-- Khu vực ảnh đã có trong hệ thống -->
                        <div class="small text-muted mb-1 fw-bold" style="font-size: 10px;">ẢNH ĐÃ LƯU TRÊN HỆ THỐNG:</div>
                        <div id="edit_gallery_container" class="row g-2 border rounded-3 p-2 bg-slate-50 mb-3 overflow-auto shadow-inner" style="height: 110px;">
                            <!-- Dữ liệu sẽ được render từ JS trong index.blade.php -->
                        </div>
                        
                        <!-- Khu vực xem trước ảnh mới chọn từ máy tính -->
                        <div class="small text-primary mb-1 fw-bold" style="font-size: 10px;">ẢNH MỚI CHỜ TẢI LÊN:</div>
                        <div id="new_gallery_preview_container" class="row g-2 border rounded-3 p-2 bg-white mb-3 overflow-auto shadow-inner" style="height: 110px;">
                            <div class="col-12 text-center py-4 text-muted x-small italic">Chưa chọn ảnh mới</div>
                        </div>

                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-info"><i class="bi bi-images"></i></span>
                            <input type="file" name="gallery[]" class="form-control rounded-end-3 shadow-none border-slate-200" accept="image/*" multiple onchange="previewNewGallery(this)">
                        </div>
                        <div class="form-text x-small text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i> Có thể chọn nhiều file cùng lúc (Giữ phím Ctrl).
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                    <i class="bi bi-save2 me-1"></i> LƯU THAY ĐỔI
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * Hàm xem trước ảnh đại diện chính khi Admin chọn file mới
     */
    function previewMainImage(input) {
        const preview = document.getElementById('edit_img_preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) { 
                preview.src = e.target.result; 
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    /**
     * Hàm xem trước danh sách ảnh Gallery mới chọn (trước khi bấm gửi form)
     */
    function previewNewGallery(input) {
        const container = document.getElementById('new_gallery_preview_container');
        container.innerHTML = ''; 
        
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-3';
                    col.innerHTML = `
                        <div class="gallery-item-wrapper border border-info rounded shadow-sm">
                            <img src="${e.target.result}" class="img-fluid rounded">
                        </div>`;
                    container.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
        } else {
            container.innerHTML = '<div class="col-12 text-center py-4 text-muted x-small italic">Chưa chọn ảnh mới</div>';
        }
    }

    /**
     * HÀM QUAN TRỌNG: Xóa ảnh gallery bằng AJAX (Fetch API)
     * Giúp xóa ảnh mà không đóng Modal, không load lại trang
     */
    async function deleteGalleryImageAjax(imageId, btnElement) {
        if (!confirm('Bạn có chắc chắn muốn xóa ảnh này khỏi thư viện?')) return;

        // Cấu hình URL xóa (Khớp với cấu trúc Route Clean URL)
        const jsBaseUrl = '{{ rtrim(BASE_URL, "/") }}'.replace('/index.php', '');
        const url = `${jsBaseUrl}/adminproduct/deleteGalleryImage/${imageId}`;

        // Hiệu ứng loading nhẹ
        btnElement.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btnElement.style.pointerEvents = 'none';

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            // Nếu xóa thành công (Controller xóa file và DB xong)
            if (response.ok) {
                // Tìm thẻ div bao quanh ảnh và xóa khỏi giao diện
                const itemWrapper = btnElement.closest('.col-3') || btnElement.closest('.gallery-item-wrapper').parentElement;
                itemWrapper.style.transition = '0.3s';
                itemWrapper.style.opacity = '0';
                setTimeout(() => {
                    itemWrapper.remove();
                    // Kiểm tra nếu hết ảnh thì hiện thông báo trống
                    const container = document.getElementById('edit_gallery_container');
                    if (container && container.querySelectorAll('.gallery-item-wrapper').length === 0) {
                        container.innerHTML = '<div class="col-12 text-center py-3 text-muted x-small italic">Thư viện ảnh trống.</div>';
                    }
                }, 300);
            } else {
                alert('Không thể xóa ảnh. Vui lòng kiểm tra lại quyền truy cập file!');
                btnElement.innerHTML = '<i class="bi bi-x"></i>';
                btnElement.style.pointerEvents = 'auto';
            }
        } catch (error) {
            console.error('Lỗi xóa ảnh:', error);
            alert('Lỗi kết nối máy chủ!');
            btnElement.innerHTML = '<i class="bi bi-x"></i>';
            btnElement.style.pointerEvents = 'auto';
        }
    }
</script>

<style>
    /* Kiểu chữ và màu sắc phụ trợ */
    .x-small { font-size: 11px; }
    .bg-slate-50 { background-color: #f8fafc; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    
    /* Tùy chỉnh giao diện thanh cuộn cho khu vực ảnh Gallery */
    #edit_gallery_container::-webkit-scrollbar, 
    #new_gallery_preview_container::-webkit-scrollbar { width: 4px; }
    #edit_gallery_container::-webkit-scrollbar-thumb, 
    #new_gallery_preview_container::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 10px; 
    }
    
    /* Thiết lập khung hiển thị ảnh Gallery tỉ lệ 1:1 */
    .gallery-item-wrapper { 
        position: relative; 
        width: 100%; 
        padding-top: 100%; 
        overflow: hidden; 
        background: #fff; 
        border-radius: 8px;
    }
    .gallery-item-wrapper img { 
        position: absolute; 
        top: 0; 
        left: 0; 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }
    
    /* Kiểu dáng cho nút xóa ảnh đơn lẻ trong Gallery (Ảnh đã lưu) */
    .btn-delete-gallery-img { 
        position: absolute; 
        top: -4px; 
        right: -4px; 
        padding: 0px 5px; 
        font-size: 10px; 
        z-index: 10; 
        border-radius: 50%; 
        line-height: 1.4; 
        box-shadow: 0 2px 4px rgba(0,0,0,0.3); 
        border: 2px solid white;
    }
</style>