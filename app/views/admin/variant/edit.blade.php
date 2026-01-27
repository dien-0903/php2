<div class="modal fade" id="editVariantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editVariantForm" action="" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title fw-bold text-uppercase">
                    <i class="bi bi-pencil-square me-2"></i>Cập nhật phiên bản
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4 text-dark">
                @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'edit')
                    <div class="alert alert-danger border-0 shadow-sm mb-4 small animate-shake">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $_SESSION['error'] }}
                    </div>
                @endif

                <input type="hidden" name="product_id" id="edit_product_id">

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted uppercase">Mã SKU</label>
                    <input type="text" name="sku" id="edit_sku" 
                           class="form-control rounded-3 shadow-none border-slate-200 py-2 fw-bold" 
                           placeholder="VD: IP15-BLACK-128" required>
                    <div class="form-text extra-small italic text-warning">Lưu ý: Không nên đổi SKU nếu sản phẩm đã có đơn hàng.</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted uppercase">Màu sắc</label>
                        <select name="color_id" id="edit_color_id" class="form-select rounded-3 shadow-none border-slate-200">
                            <option value="">-- Mặc định --</option>
                            @foreach($colors as $c)
                                <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted uppercase">Kích thước</label>
                        <select name="size_id" id="edit_size_id" class="form-select rounded-3 shadow-none border-slate-200">
                            <option value="">-- Mặc định --</option>
                            @foreach($sizes as $s)
                                <option value="{{ $s['id'] }}">{{ $s['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted uppercase">Giá bán (đ)</label>
                        <input type="number" name="price" id="edit_price" 
                               class="form-control rounded-3 shadow-none border-slate-200" required min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold text-muted uppercase">Tồn kho</label>
                        <input type="number" name="stock" id="edit_stock" 
                               class="form-control rounded-3 shadow-none border-slate-200" required min="0">
                    </div>
                </div>

                <div class="row align-items-center mt-4">
                    <div class="col-3 text-center">
                        <div class="extra-small text-muted mb-1 uppercase fw-bold">Ảnh cũ</div>
                        <img id="edit_img_preview" src="" 
                             class="img-fluid rounded-3 border shadow-sm" 
                             style="width: 65px; height: 65px; object-fit: cover;"
                             onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                    </div>
                    <div class="col-9">
                        <label class="form-label small fw-bold text-muted uppercase">Thay đổi ảnh mới</label>
                        <input type="file" name="image" class="form-control rounded-3 shadow-none border-slate-200" accept="image/*">
                        <div class="form-text extra-small italic">Để trống nếu muốn giữ nguyên ảnh hiện tại.</div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">HỦY BỎ</button>
                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> XÁC NHẬN CẬP NHẬT
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.btn-edit-variant');
    const editForm = document.getElementById('editVariantForm');
    const imgPreview = document.getElementById('edit_img_preview');
    
    const jsBaseUrl = '{{ rtrim(BASE_URL, "/") }}';

    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const productId = this.dataset.productid;
            const sku = this.dataset.sku;
            const colorId = this.dataset.color;
            const sizeId = this.dataset.size;
            const price = this.dataset.price;
            const stock = this.dataset.stock;
            const image = this.dataset.image;

            editForm.action = jsBaseUrl + '/adminvariant/update/' + id;
            document.getElementById('edit_product_id').value = productId;
            document.getElementById('edit_sku').value = sku;
            document.getElementById('edit_color_id').value = colorId || "";
            document.getElementById('edit_size_id').value = sizeId || "";
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_stock').value = stock;
            
            imgPreview.src = (image && image !== 'default.jpg') 
                ? jsBaseUrl + '/public/uploads/products/' + image 
                : 'https://placehold.co/100x100?text=No+Image';
        });
    });
});
</script>

<style>
    .extra-small { font-size: 10px; }
    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    
    #editVariantModal .modal-header {
        border-bottom: 3px solid #ffc107 !important;
    }
</style>