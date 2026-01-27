<div class="modal fade" id="editColorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editColorForm" action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold text-uppercase">Cập nhật màu sắc</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted uppercase">Tên Màu</label>
                    <input type="text" name="name" id="edit_color_name" class="form-control rounded-3 border-slate-200 shadow-none" required>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted uppercase">Mã màu (Hex)</label>
                    <div class="d-flex gap-2">
                        <input type="color" id="edit_picker" class="form-control form-control-color border-0 p-0 rounded-3" 
                               style="width: 55px; height: 45px; cursor: pointer;">
                        <input type="text" name="hex_code" id="edit_color_hex" 
                               class="form-control rounded-3 border-slate-200 shadow-none fw-bold text-uppercase" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">ĐÓNG</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">CẬP NHẬT NGAY</button>
            </div>
        </form>
    </div>
</div>

<script>
    const editPicker = document.getElementById('edit_picker');
    const editHexText = document.getElementById('edit_color_hex');
    if(editPicker && editHexText) {
        editPicker.addEventListener('input', () => editHexText.value = editPicker.value.toUpperCase());
        editHexText.addEventListener('input', () => {
            if(/^#[0-9A-F]{6}$/i.test(editHexText.value)) editPicker.value = editHexText.value;
        });
    }
</script>