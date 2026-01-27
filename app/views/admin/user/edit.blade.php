<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editUserForm" action="" method="POST" class="modal-content shadow-lg border-0 rounded-4">
            
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="bi bi-pencil-square me-2"></i>Cập Nhật Thành Viên
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

                <div class="mb-3">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Họ và Tên</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-person-fill text-muted"></i></span>
                        <input type="text" name="fullname" id="edit_fullname"
                               value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['fullname'] ?? '') : '' }}"
                               class="form-control bg-transparent border-0 py-2 shadow-none" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Email (Không đổi nếu giữ nguyên)</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-envelope-fill text-muted"></i></span>
                        <input type="email" name="email" id="edit_email"
                               value="{{ (($_SESSION['error_type'] ?? '') === 'edit') ? ($_SESSION['old']['email'] ?? '') : '' }}"
                               class="form-control bg-transparent border-0 py-2 shadow-none" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Vai trò hệ thống</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-shield-lock-fill text-muted"></i></span>
                        <select name="role" id="edit_role" class="form-select bg-transparent border-0 py-2 shadow-none">
                            <option value="user">Người dùng (Khách hàng)</option>
                            <option value="admin">Quản trị viên (Toàn quyền)</option>
                        </select>
                    </div>
                </div>

                <div class="alert alert-secondary border-0 shadow-sm rounded-3 mt-4 py-2 small">
                    <i class="bi bi-info-circle-fill me-1"></i> Tính năng đổi mật khẩu đã bị vô hiệu hóa tại đây để đảm bảo an toàn bảo mật.
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

@php
    if (isset($_SESSION['error_type']) && $_SESSION['error_type'] === 'edit') {
        unset($_SESSION['error']);
        unset($_SESSION['error_type']);
        unset($_SESSION['old']);
    }
@endphp

<style>
    @keyframes shakeUserEdit {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shakeUserEdit 0.3s ease-in-out; }
</style>