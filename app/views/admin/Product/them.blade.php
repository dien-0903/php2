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
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">TÊN SẢN PHẨM</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-none" placeholder="Nhập tên sản phẩm công nghệ..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">GIÁ BÁN (VNĐ)</label>
                        <input type="number" name="price" class="form-control rounded-3 shadow-none" min="0" placeholder="Ví dụ: 15000000" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">DANH MỤC</label>
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
                        <label class="form-label fw-bold small">THƯƠNG HIỆU</label>
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
                <div class="mb-0">
                    <label class="form-label fw-bold small">HÌNH ẢNH ĐẠI DIỆN</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light rounded-start-3 border-end-0"><i class="bi bi-image"></i></span>
                        <input type="file" name="image" class="form-control rounded-end-3 shadow-none" accept="image/*">
                    </div>
                    <div class="form-text mt-2 text-muted">
                        <i class="bi bi-info-circle me-1"></i> Định dạng hỗ trợ: JPG, PNG, WebP. Tối đa 2MB.
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>