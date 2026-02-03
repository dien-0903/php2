<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editProductForm" action="" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0">
            
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Cập Nhật Sản Phẩm
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-dark">
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

                <div class="mb-3">
                    <label class="form-label fw-bold small text-secondary">MÔ TẢ CHI TIẾT</label>
                    <textarea name="description" id="edit_description" class="form-control rounded-3 shadow-none border-slate-200" rows="3"></textarea>
                </div>

                <div class="row mb-3">
                    <!-- PHẦN ẢNH ĐẠI DIỆN -->
                    <div class="col-md-6 border-end">
                        <label class="form-label fw-bold small text-secondary text-uppercase">Ảnh đại diện</label>
                        <div class="bg-light rounded-4 p-2 border mb-2 text-center d-flex align-items-center justify-content-center shadow-inner" style="height: 220px; overflow: hidden;">
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

                    <!-- PHẦN THƯ VIỆN ẢNH -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary text-uppercase">Thư viện ảnh</label>
                        
                        <div class="small text-muted mb-1" style="font-size: 10px; font-weight: 800;">ĐÃ LƯU:</div>
                        <div id="edit_gallery_container" class="row g-2 border rounded-3 p-2 bg-slate-50 mb-3 overflow-auto shadow-inner" style="height: 110px;">
                            <!-- JS sẽ đổ ảnh cũ vào đây -->
                        </div>
                        
                        <div class="small text-primary mb-1" style="font-size: 10px; font-weight: 800;">MỚI CHỌN:</div>
                        <div id="new_gallery_preview_container" class="row g-2 border rounded-3 p-2 bg-white mb-2 overflow-auto shadow-inner" style="height: 110px;">
                            <div class="col-12 text-center py-4 text-muted x-small italic">Chưa có ảnh mới</div>
                        </div>

                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white border-end-0 text-info"><i class="bi bi-images"></i></span>
                            <input type="file" name="gallery[]" class="form-control rounded-end-3 shadow-none border-slate-200" accept="image/*" multiple onchange="previewNewGallery(this)">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                    LƯU THAY ĐỔI
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewMainImage(input) {
        const preview = document.getElementById('edit_img_preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewNewGallery(input) {
        const container = document.getElementById('new_gallery_preview_container');
        container.innerHTML = ''; 
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-4';
                    col.innerHTML = `<div class="gallery-item-wrapper border border-info rounded"><img src="${e.target.result}" class="img-fluid rounded"></div>`;
                    container.appendChild(col);
                }
                reader.readAsDataURL(file);
            });
        } else {
            container.innerHTML = '<div class="col-12 text-center py-4 text-muted x-small italic">Chưa có ảnh mới</div>';
        }
    }
</script>

<style>
    .x-small { font-size: 11px; }
    .bg-slate-50 { background-color: #f8fafc; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    #edit_gallery_container::-webkit-scrollbar, #new_gallery_preview_container::-webkit-scrollbar { width: 4px; }
    #edit_gallery_container::-webkit-scrollbar-thumb, #new_gallery_preview_container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .gallery-item-wrapper { position: relative; width: 100%; padding-top: 100%; overflow: hidden; background: #fff; }
    .gallery-item-wrapper img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
    .btn-delete-gallery-img { position: absolute; top: -4px; right: -4px; padding: 0px 5px; font-size: 10px; z-index: 10; border-radius: 50%; line-height: 1.4; box-shadow: 0 2px 4px rgba(0,0,0,0.3); }
</style>