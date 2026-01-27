<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editProductForm" action="" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0">
            
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Cập Nhật Thông Tin Sản Phẩm
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-dark">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'edit')
                    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center animate-shake">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ $_SESSION['error'] }}</div>
                    </div>
                @endif

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">TÊN SẢN PHẨM</label>
                        <input type="text" name="name" id="edit_name" 
                               value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['name'] ?? '') : '' }}"
                               class="form-control rounded-3 shadow-none border-slate-200" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">GIÁ BÁN (VNĐ)</label>
                        <input type="number" name="price" id="edit_price" 
                               value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['price'] ?? '') : '' }}"
                               class="form-control rounded-3 shadow-none border-slate-200" min="0" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">DANH MỤC</label>
                        <select name="category_id" id="edit_category_id" class="form-select rounded-3 shadow-none border-slate-200" required>
                            @if(!empty($all_categories))
                                @foreach ($all_categories as $c)
                                    <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">THƯƠNG HIỆU</label>
                        <select name="brand_id" id="edit_brand_id" class="form-select rounded-3 shadow-none border-slate-200" required>
                            @if(!empty($all_brands))
                                @foreach ($all_brands as $b)
                                    <option value="{{ $b['id'] }}">{{ $b['name'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-3 text-center">
                        <div class="mb-2 small text-muted font-bold uppercase" style="font-size: 10px;">Ảnh hiện tại</div>
                        <div class="bg-light rounded-3 p-2 border border-slate-100 shadow-sm">
                            <img id="edit_img_preview" src="" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
                        </div>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label fw-bold small">THAY ĐỔI HÌNH ẢNH (KHÔNG CHỌN NẾU GIỮ CŨ)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 border-slate-200"><i class="bi bi-image"></i></span>
                            <input type="file" name="image" class="form-control rounded-end-3 shadow-none border-slate-200" accept="image/*">
                        </div>
                        <div class="form-text mt-2 text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Để trống nếu bạn muốn giữ lại hình ảnh đang hiển thị bên cạnh.
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                    LƯU THAY ĐỔI
                </button>
            </div>
        </form>
    </div>
</div>

@php
    if (isset($_SESSION['error_type']) && $_SESSION['error_type'] === 'edit') {
        unset($_SESSION['error']);
        unset($_SESSION['error_type']);
        unset($_SESSION['old']);
    }
@endphp

<style>
    @keyframes shakeEdit {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-6px); }
        40%, 80% { transform: translateX(6px); }
    }
    .animate-shake { animation: shakeEdit 0.4s ease-in-out; }
    
    #editProductModal .modal-header {
        border-bottom: 2px solid #ffc107;
    }
</style>