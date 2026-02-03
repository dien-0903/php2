<!-- Modal Sửa User -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Cập nhật thông tin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Action form sẽ được JS cập nhật -->
            <form id="editUserForm" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="fullname" id="edit_fullname" class="form-control rounded-3 py-2" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3 py-2" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Vai trò</label>
                        <select name="role" id="edit_role" class="form-select rounded-3 py-2">
                            <option value="user">Người dùng (User)</option>
                            <option value="admin">Quản trị viên (Admin)</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-light border small text-muted">
                        <i class="bi bi-info-circle me-1"></i> Để đổi mật khẩu, vui lòng sử dụng nút "Đổi mật khẩu" bên ngoài danh sách.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-save me-1"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>  