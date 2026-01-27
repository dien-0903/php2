@php
    $prefix = $product ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product['name']), 0, 3)) : 'SKU';
@endphp

<div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 100px;">
    <div class="card-header bg-primary text-white py-3 fw-bold border-0 text-center text-uppercase">
        <i class="bi bi-plus-lg me-1"></i>Tạo phiên bản mới
    </div>
    
    <div class="card-body p-4 bg-white text-dark">
        @if(isset($_SESSION['error']) && ($_SESSION['error_type'] ?? '') === 'add')
            <div class="alert alert-danger border-0 shadow-sm mb-4 small animate-shake">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $_SESSION['error'] }}
            </div>
            @php 
                $old = $_SESSION['old'] ?? [];
                unset($_SESSION['error'], $_SESSION['error_type'], $_SESSION['old']); 
            @endphp
        @endif

        <form action="{{ BASE_URL }}/adminvariant/store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="{{ $product['id'] ?? '' }}">
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted uppercase">Mã SKU (Định danh kho)</label>
                <input type="text" name="sku" 
                       value="{{ $old['sku'] ?? '' }}"
                       class="form-control rounded-3 shadow-none border-slate-200 py-2" 
                       placeholder="VD: {{ $prefix }}-BLACK-128" required>
                <div class="form-text extra-small italic">Mã SKU phải là duy nhất cho mỗi phiên bản.</div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted uppercase">Màu sắc</label>
                    <select name="color_id" class="form-select rounded-3 shadow-none border-slate-200">
                        <option value="">-- Mặc định --</option>
                        @foreach($colors as $c)
                            <option value="{{ $c['id'] }}" {{ ($old['color_id'] ?? '') == $c['id'] ? 'selected' : '' }}>
                                {{ $c['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted uppercase">Kích thước</label>
                    <select name="size_id" class="form-select rounded-3 shadow-none border-slate-200">
                        <option value="">-- Mặc định --</option>
                        @foreach($sizes as $s)
                            <option value="{{ $s['id'] }}" {{ ($old['size_id'] ?? '') == $s['id'] ? 'selected' : '' }}>
                                {{ $s['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted uppercase">Giá bán (đ)</label>
                    <input type="number" name="price" 
                           value="{{ $old['price'] ?? ($product['price'] ?? 0) }}"
                           class="form-control rounded-3 shadow-none border-slate-200" required min="0">
                </div>
                <div class="col-6">
                    <label class="form-label small fw-bold text-muted uppercase">Tồn kho</label>
                    <input type="number" name="stock" 
                           value="{{ $old['stock'] ?? 10 }}"
                           class="form-control rounded-3 shadow-none border-slate-200" required min="0">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted uppercase">Ảnh phiên bản</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-slate-200 text-muted"><i class="bi bi-image"></i></span>
                    <input type="file" name="image" class="form-control rounded-end-3 shadow-none border-slate-200" accept="image/*">
                </div>
                <div class="form-text extra-small mt-2 text-primary">Tải ảnh lên nếu phiên bản có màu sắc/kiểu dáng khác ảnh gốc.</div>
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold shadow-sm transition-all hover:bg-dark border-0">
                <i class="bi bi-cloud-arrow-up me-1"></i> LƯU BIẾN THỂ
            </button>
        </form>
    </div>
</div>

<style>
    .extra-small { font-size: 10px; }
    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
</style>