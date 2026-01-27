<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ BASE_URL }}/admincategory/store" method="POST" enctype="multipart/form-data" class="modal-content shadow-lg border-0 rounded-4">
            
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2"></i>Thêm Danh Mục Mới
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
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Tên danh mục</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-tag-fill text-muted"></i></span>
                        <input type="text" name="name" 
                               value="{{ ($_SESSION['error_type'] ?? '') === 'add' ? ($_SESSION['old']['name'] ?? '') : '' }}"
                               class="form-control bg-transparent border-0 py-2 shadow-none" 
                               placeholder="Ví dụ: Điện thoại, Laptop..." required>
                    </div>
                    <div class="form-text mt-1 extra-small italic">Gợi ý: Tên danh mục nên ngắn gọn, dễ hiểu.</div>
                </div>

                <div class="mb-0">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Ảnh đại diện danh mục</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-image text-muted"></i></span>
                        <input type="file" name="image" class="form-control bg-transparent border-0 py-2 shadow-none" accept="image/*">
                    </div>
                    <div class="form-text mt-2 extra-small text-muted italic">
                        <i class="bi bi-info-circle me-1"></i> Định dạng: .jpg, .png, .webp (Dưới 2MB).
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    LƯU DANH MỤC
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
</style>