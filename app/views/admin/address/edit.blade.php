<!-- Modal Chỉnh Sửa Địa Chỉ (Dùng chung cho hệ thống quản trị) -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <!-- Form này sẽ được JavaScript trong index.blade.php điều khiển action -->
        <form id="editAddressForm" method="POST" class="modal-content border-0 shadow-lg rounded-4 text-start">
            <!-- ID của khách hàng để Controller biết quay lại đúng danh sách sau khi lưu -->
            <input type="hidden" name="user_id" value="{{ $user['id'] }}">
            
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square me-2"></i>Chỉnh sửa địa chỉ
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 text-dark">
                <!-- Trường Họ tên -->
                <div class="mb-3">
                    <label class="form-label small fw-bold uppercase text-muted">Họ tên người nhận</label>
                    <input type="text" name="recipient_name" id="address_edit_name" 
                           class="form-control rounded-3 py-2 border-slate-200 shadow-none focus-ring-warning" 
                           required placeholder="Nhập tên người nhận">
                </div>

                <!-- Trường Số điện thoại (Sử dụng ID duy nhất để tránh lỗi JS) -->
                <div class="mb-3">
                    <label class="form-label small fw-bold uppercase text-muted">Số điện thoại</label>
                    <input type="text" name="phone" id="address_edit_phone" 
                           class="form-control rounded-3 py-2 border-slate-200 shadow-none focus-ring-warning" 
                           required placeholder="09xxxxxxxx">
                </div>

                <!-- Bộ chọn địa chỉ 3 cấp (Dữ liệu do API nạp vào) -->
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <label class="form-label extra-small fw-bold text-muted uppercase">Tỉnh/Thành</label>
                        <select class="form-select rounded-3 border-slate-200 shadow-none province-select" required>
                            <option value="" disabled selected>Chọn...</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label extra-small fw-bold text-muted uppercase">Quận/Huyện</label>
                        <select class="form-select rounded-3 border-slate-200 shadow-none district-select" required disabled>
                            <option value="" disabled selected>Chọn...</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label extra-small fw-bold text-muted uppercase">Phường/Xã</label>
                        <select class="form-select rounded-3 border-slate-200 shadow-none ward-select" required disabled>
                            <option value="" disabled selected>Chọn...</option>
                        </select>
                    </div>
                </div>

                <!-- Trường địa chỉ chi tiết -->
                <div class="mb-3">
                    <label class="form-label small fw-bold uppercase text-muted">Số nhà, tên đường</label>
                    <input type="text" class="form-control rounded-3 py-2 border-slate-200 shadow-none street-input" 
                           required placeholder="Ví dụ: 123 Đường Lê Lợi...">
                </div>

                <!-- Trường ẩn dùng để gộp địa chỉ trước khi gửi về Model trong Canvas -->
                <input type="hidden" name="address" class="address-full-hidden">

                <!-- Đặt làm mặc định -->
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input shadow-none" type="checkbox" name="is_default" id="address_edit_default" value="1">
                    <label class="form-check-label small fw-bold" for="address_edit_default">Đặt làm địa chỉ mặc định</label>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0 mt-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">HỦY BỎ</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm text-dark">
                    <i class="bi bi-save2 me-1"></i> CẬP NHẬT
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .extra-small { font-size: 10px; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .focus-ring-warning:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.25);
    }
</style>