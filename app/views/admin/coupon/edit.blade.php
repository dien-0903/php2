<div class="modal fade" id="editCouponModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editCouponForm" action="" method="POST" class="modal-content shadow-lg border-0 rounded-4">
            
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Cập Nhật Mã Giảm Giá
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

                <div class="mb-4">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Mã giảm giá (Code)</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-tag-fill text-warning"></i></span>
                        <input type="text" id="edit_code" name="code" 
                               class="form-control bg-transparent border-0 py-2 shadow-none fw-bold text-uppercase" 
                               readonly style="cursor: not-allowed;">
                    </div>
                    <div class="form-text mt-1 extra-small italic text-muted">Mã code là định danh duy nhất và không được phép sửa.</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small uppercase text-muted tracking-wider">Loại giảm giá</label>
                        <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-layers-half text-muted"></i></span>
                            <select name="type" id="edit_type" class="form-select bg-transparent border-0 py-2 shadow-none">
                                <option value="percent">Phần trăm (%)</option>
                                <option value="fixed">Tiền mặt (đ)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small uppercase text-muted tracking-wider">Giá trị giảm</label>
                        <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-cash-coin text-muted"></i></span>
                            <input type="number" name="value" id="edit_value" 
                                   value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['value'] ?? '') : '' }}"
                                   class="form-control bg-transparent border-0 py-2 shadow-none" 
                                   placeholder="Nhập số..." required min="1">
                        </div>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-3 border border-slate-100">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input shadow-none cursor-pointer" type="checkbox" 
                               name="status" value="1" id="edit_status">
                        <label class="form-check-label fw-bold text-dark cursor-pointer" for="edit_status">
                            Kích hoạt mã này ngay bây giờ
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
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
    .extra-small { font-size: 11px; }
    @keyframes shakeEdit {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shakeEdit 0.3s ease-in-out; }
    .cursor-pointer { cursor: pointer; }
</style>