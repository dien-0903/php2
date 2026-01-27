<div class="modal fade" id="addSizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ BASE_URL }}/adminsize/store" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold text-uppercase">Thêm kích thước mới</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <label class="form-label small fw-bold text-muted uppercase">Tên kích thước / Thông số</label>
                <input type="text" name="name" class="form-control rounded-3 shadow-none border-slate-200 py-2" 
                       placeholder="VD: 128GB, XL, 42, 6.7 inch..." required>
                <div class="form-text mt-2 italic small">Nhập tên hiển thị cho thuộc tính sản phẩm.</div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">HỦY</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">LƯU DỮ LIỆU</button>
            </div>
        </form>
    </div>
</div>