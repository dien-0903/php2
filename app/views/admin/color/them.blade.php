<div class="modal fade" id="addColorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ BASE_URL }}/admincolor/store" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold text-uppercase">Thêm màu sắc mới</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted uppercase">Tên Màu</label>
                    <input type="text" name="name" class="form-control rounded-3 border-slate-200 shadow-none" 
                           placeholder="VD: Titan Tự Nhiên, Xanh Ocean..." required>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted uppercase">Mã màu (Hex)</label>
                    <div class="d-flex gap-2">
                        <input type="color" id="add_picker" class="form-control form-control-color border-0 p-0 rounded-3" 
                               value="#2563eb" style="width: 55px; height: 45px; cursor: pointer;">
                        <input type="text" name="hex_code" id="add_hex_text" 
                               class="form-control rounded-3 border-slate-200 shadow-none fw-bold text-uppercase" 
                               value="#2563eb" required>
                    </div>
                    <div class="form-text mt-2 small italic">Chọn màu từ bảng hoặc nhập trực tiếp mã Hex.</div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">HỦY</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">LƯU DỮ LIỆU</button>
            </div>
        </form>
    </div>
</div>

<script>
    const addPicker = document.getElementById('add_picker');
    const addHexText = document.getElementById('add_hex_text');
    if(addPicker && addHexText) {
        addPicker.addEventListener('input', () => addHexText.value = addPicker.value.toUpperCase());
        addHexText.addEventListener('input', () => {
            if(/^#[0-9A-F]{6}$/i.test(addHexText.value)) addPicker.value = addHexText.value;
        });
    }
</script>
<style>
    .form-control-color::-webkit-color-swatch { border-radius: 8px; border: 1px solid #e2e8f0; }
</style>