<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Thêm thành viên mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ rtrim(BASE_URL, '/') }}/adminuser/store" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" class="form-control rounded-3 py-2" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Email đăng nhập <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control rounded-3 py-2" required placeholder="email@example.com">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control rounded-3 py-2" required minlength="6" placeholder="******">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Vai trò</label>
                            <select name="role" class="form-select rounded-3 py-2">
                                <option value="user">Người dùng (User)</option>
                                <option value="admin">Quản trị viên (Admin)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Lưu lại
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>