<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminuser/store" method="POST" class="modal-content shadow-lg border-0 rounded-4">
            
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Thêm Thành Viên Mới
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-dark">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'add')
                    <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center animate-shake">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ $_SESSION['error'] }}</div>
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Họ và Tên</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-person-fill text-muted"></i></span>
                        <input type="text" name="fullname" 
                               value="{{ ($_SESSION['error_type'] ?? '') === 'add' ? ($_SESSION['old']['fullname'] ?? '') : '' }}"
                               class="form-control bg-transparent border-0 py-2 shadow-none" 
                               placeholder="Nguyễn Văn A" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small uppercase text-muted tracking-wider">Email đăng nhập</label>
                    <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                        <span class="input-group-text bg-transparent border-0"><i class="bi bi-envelope-fill text-muted"></i></span>
                        <input type="email" name="email" 
                               value="{{ ($_SESSION['error_type'] ?? '') === 'add' ? ($_SESSION['old']['email'] ?? '') : '' }}"
                               class="form-control bg-transparent border-0 py-2 shadow-none" 
                               placeholder="name@example.com" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label fw-bold small uppercase text-muted tracking-wider">Mật khẩu</label>
                        <div class="input-group bg-light rounded-3 overflow-hidden border border-slate-200">
                            <span class="input-group-text bg-transparent border-0"><i class="bi bi-key-fill text-muted"></i></span>
                            <input type="password" name="password" class="form-control bg-transparent border-0 py-2 shadow-none" 
                                   placeholder="••••••••" required>
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label fw-bold small uppercase text-muted tracking-wider">Vai trò</label>
                        <select name="role" class="form-select bg-light rounded-3 border-slate-200 py-2 shadow-none">
                            <option value="user" {{ (($_SESSION['error_type'] ?? '') === 'add' && ($_SESSION['old']['role'] ?? '') == 'user') ? 'selected' : '' }}>Người dùng</option>
                            <option value="admin" {{ (($_SESSION['error_type'] ?? '') === 'add' && ($_SESSION['old']['role'] ?? '') == 'admin') ? 'selected' : '' }}>Quản trị viên</option>
                        </select>
                    </div>
                </div>

                <div class="form-text mt-2 small italic text-muted">
                    <i class="bi bi-info-circle me-1"></i> Mật khẩu nên có tối thiểu 6 ký tự để đảm bảo an toàn.
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    LƯU THÀNH VIÊN
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
    @keyframes shakeUserAdd {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-shake { animation: shakeUserAdd 0.3s ease-in-out; }
</style>