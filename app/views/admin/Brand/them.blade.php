<div class="modal fade" id="addBrandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ BASE_URL }}/adminbrand/store" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0 rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>Thêm Thương Hiệu Mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'add')
                    <div class="alert alert-danger border-0 small mb-3 animate-shake">
                        <i class="bi bi-exclamation-octagon me-2"></i>{{ $_SESSION['error'] }}
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold small">TÊN THƯƠNG HIỆU</label>
                    <input type="text" name="name" 
                           value="{{ ($_SESSION['error_type'] ?? '') === 'add' ? ($_SESSION['old']['name'] ?? '') : '' }}"
                           class="form-control rounded-3 shadow-none border-slate-200" 
                           placeholder="Ví dụ: Apple, Samsung..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">MÔ TẢ NGẮN</label>
                    <textarea name="description" class="form-control rounded-3 shadow-none border-slate-200" rows="3">{{ ($_SESSION['error_type'] ?? '') === 'add' ? ($_SESSION['old']['description'] ?? '') : '' }}</textarea>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold small">LOGO (HÌNH VUÔNG SẼ ĐẸP NHẤT)</label>
                    <input type="file" name="image" class="form-control rounded-3 shadow-none border-slate-200" accept="image/*">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Lưu dữ liệu</button>
            </div>
        </form>
    </div>
</div>

@php
    if (isset($_SESSION['error_type']) && $_SESSION['error_type'] === 'add') {
        unset($_SESSION['error']); unset($_SESSION['error_type']); unset($_SESSION['old']);
    }
@endphp