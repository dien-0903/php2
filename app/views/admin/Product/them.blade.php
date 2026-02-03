<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ BASE_URL }}/adminproduct/store" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0">
            
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Thêm Sản Phẩm Mới
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-dark">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'add')
                    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                        <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                        <div>{{ $_SESSION['error'] }}</div>
                        @php unset($_SESSION['error']); unset($_SESSION['error_type']); @endphp
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">TÊN SẢN PHẨM</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-none" placeholder="Nhập tên sản phẩm..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">GIÁ BÁN (VNĐ)</label>
                        <input type="number" name="price" class="form-control rounded-3 shadow-none" min="0" placeholder="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">TỒN KHO</label>
                        <input type="number" name="stock" class="form-control rounded-3 shadow-none" min="0" value="0">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">DANH MỤC</label>
                        <select name="category_id" class="form-select rounded-3 shadow-none" required>
                            <option value="">-- Chọn danh mục --</option>
                            @if(!empty($all_categories))
                                @foreach ($all_categories as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">THƯƠNG HIỆU</label>
                        <select name="brand_id" class="form-select rounded-3 shadow-none" required>
                            <option value="">-- Chọn thương hiệu --</option>
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
                    <textarea name="description" class="form-control rounded-3 shadow-none" rows="3" placeholder="Thông số kỹ thuật..."></textarea>
                </div>

                <div class="row mb-0">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">ẢNH ĐẠI DIỆN</label>
                        <input type="file" name="image" class="form-control rounded-3 mb-2" accept="image/*" onchange="previewMainImage(this, 'add_img_preview')">
                        <div class="text-center bg-light rounded-3 p-2 border">
                            <img id="add_img_preview" src="https://placehold.co/200x200?text=Preview" class="img-fluid rounded" style="max-height: 150px;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-secondary">THƯ VIỆN ẢNH (CHỌN NHIỀU)</label>
                        <input type="file" name="gallery[]" class="form-control rounded-3" accept="image/*" multiple>
                        <div class="form-text mt-2 small text-muted">Giữ phím Ctrl để chọn nhiều ảnh cùng lúc.</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Lưu sản phẩm</button>
            </div>
        </form>
    </div>
</div>