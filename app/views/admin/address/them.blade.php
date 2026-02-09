<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminaddress/store" method="POST" id="addAddressForm" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="user_id" value="{{ $user['id'] }}">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold uppercase text-muted">Tên người nhận</label>
                    <input type="text" name="recipient_name" class="form-control rounded-3 py-2" required value="{{ $user['fullname'] }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold uppercase text-muted">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control rounded-3 py-2" required value="{{ $user['phone'] ?? '' }}" placeholder="09xxxxxxxx">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4"><select class="form-select province-select rounded-3" required><option value="" disabled selected>Tỉnh</option></select></div>
                    <div class="col-4"><select class="form-select district-select rounded-3" required disabled><option value="" disabled selected>Huyện</option></select></div>
                    <div class="col-4"><select class="form-select ward-select rounded-3" required disabled><option value="" disabled selected>Xã</option></select></div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold uppercase text-muted">Số nhà, tên đường</label>
                    <input type="text" id="street" class="form-control street-input rounded-3 py-2" required placeholder="Ví dụ: 123 Lê Lợi...">
                </div>
                <input type="hidden" name="address" class="address-full-hidden">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="defaultAdd">
                    <label class="form-check-label small fw-bold" for="defaultAdd">Đặt làm mặc định</label>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">LƯU ĐỊA CHỈ</button>
            </div>
        </form>
    </div>
</div>