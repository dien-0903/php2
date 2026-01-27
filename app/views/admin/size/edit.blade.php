<div class="modal fade" id="editSizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editSizeForm" action="" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold text-uppercase">Cập nhật kích thước</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <label class="form-label small fw-bold text-muted uppercase">Tên kích thước / Thông số</label>
                <input type="text" name="name" id="edit_size_name" class="form-control rounded-3 shadow-none border-slate-200 py-2" required>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">ĐÓNG</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">CẬP NHẬT NGAY</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.btn-edit-size');
    const editForm = document.getElementById('editSizeForm');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            editForm.action = '{{ BASE_URL }}/adminsize/update/' + this.dataset.id;
            document.getElementById('edit_size_name').value = this.dataset.name;
        });
    });
});
</script>