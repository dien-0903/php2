<div class="modal fade" id="addCouponModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ BASE_URL }}/admincoupon/store" method="POST" class="modal-content shadow-lg border-0 rounded-4">
            
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Thêm Mã Giảm Giá Mới
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-dark">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'add')
                    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center animate-shake">
                        <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                        <div>{{ $_SESSION['error'] }}</div>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Mã giảm giá (Code)</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-tag-fill text-primary"></i></span>
                        <input type="text" name="code" 
                               value="{{ ($_SESSION['error_type'] ?? '') === 'add' ? ($_SESSION['old']['code'] ?? '') : '' }}"
                               class="form-control bg-transparent border-0 py-2 shadow-none fw-bold text-uppercase" 
                               placeholder="VD: MD20, TET2026..." required autocomplete="off">
                    </div>
                    <div class="form-text mt-1 extra-small italic text-muted">Nên viết liền, không dấu (Ví dụ: KM50K).</div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small uppercase text-muted tracking-wider">Loại giảm giá</label>
                        <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-layers-half text-muted"></i></span>
                            <select name="type" class="form-select bg-transparent border-0 py-2 shadow-none">
                                <option value="percent" {{ (($_SESSION['error_type'] ?? '') === 'add' && ($_SESSION['old']['type'] ?? '') == 'percent') ? 'selected' : '' }}>Phần trăm (%)</option>
                                <option value="fixed" {{ (($_SESSION['error_type'] ?? '') === 'add' && ($_SESSION['old']['type'] ?? '') == 'fixed') ? 'selected' : '' }}>Tiền mặt (đ)</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label fw-bold small uppercase text-muted tracking-wider">Giá trị giảm</label>
                        <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-cash-coin text-muted"></i></span>
                            <input type="number" name="value" 
                                   value="{{ ($_SESSION['error_type'] ?? '') === 'add' ? ($_SESSION['old']['value'] ?? '') : '' }}"
                                   class="form-control bg-transparent border-0 py-2 shadow-none" 
                                   placeholder="Nhập số..." required min="1">
                        </div>
                    </div>
                </div>

                <div class="bg-light p-3 rounded-3 border border-slate-100">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input shadow-none cursor-pointer" type="checkbox" 
                               name="status" value="1" id="add_status" checked>
                        <label class="form-check-label fw-bold text-dark cursor-pointer" for="add_status">
                            Kích hoạt mã giảm giá này ngay
                        </label>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    TẠO MÃ NGAY
                </button>
            </div>
        </form>
    </div>
</div>

@php
    if (isset($_SESSION['error_type']) && $_SESSION['error_type'] === 'add') {
        unset($_SESSION['error']);
        unset($_SESSION['error_type']);
        unset($_SESSION['old']);
    }
@endphp

<style>
    .extra-small { font-size: 11px; }
    @keyframes shakeAdd {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shakeAdd 0.3s ease-in-out; }
    .cursor-pointer { cursor: pointer; }
</style>