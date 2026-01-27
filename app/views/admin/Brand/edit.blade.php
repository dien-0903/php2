<div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editBrandForm" action="" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Cập Nhật Thương Hiệu</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'edit')
                    <div class="alert alert-danger border-0 small mb-3 animate-shake">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ $_SESSION['error'] }}
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold small">TÊN THƯƠNG HIỆU</label>
                    <input type="text" name="name" id="edit_brand_name" 
                           value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['name'] ?? '') : '' }}"
                           class="form-control rounded-3 shadow-none border-slate-200" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">MÔ TẢ</label>
                    <textarea name="description" id="edit_brand_description" class="form-control rounded-3 shadow-none border-slate-200" rows="3">{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['description'] ?? '') : '' }}</textarea>
                </div>

                <div class="row align-items-center">
                    <div class="col-3 text-center">
                        <img id="edit_brand_img_preview" src="" class="rounded border p-1" style="width: 60px; height: 60px; object-fit: contain;">
                    </div>
                    <div class="col-9">
                        <label class="form-label fw-bold small">THAY LOGO MỚI</label>
                        <input type="file" name="image" class="form-control rounded-3 shadow-none border-slate-200" accept="image/*">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">Cập nhật ngay</button>
            </div>
        </form>
    </div>
</div>

@php
    if (isset($_SESSION['error_type']) && $_SESSION['error_type'] === 'edit') {
        unset($_SESSION['error']); unset($_SESSION['error_type']); unset($_SESSION['old']);
    }
@endphp

<style>
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shake 0.3s ease-in-out; }
    .object-fit-contain { object-fit: contain; }
</style>