@include('admin.layouts.header')   
<div class="container mt-4 text-dark">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center animate-slide-down">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    @if(isset($error_msg))
        <div class="alert alert-warning border-0 shadow-sm rounded-4 p-5 text-center mb-5">
            <i class="bi bi-exclamation-octagon fs-1 d-block mb-3 text-warning"></i>
            <h4 class="fw-bold">Rất tiếc!</h4>
            <p class="text-muted">{{ $error_msg }}</p>
            <a href="{{ BASE_URL }}/adminproduct/index" class="btn btn-dark rounded-pill px-4 mt-2">QUAY LẠI DANH SÁCH</a>
        </div>
    @endif
    @if($product)
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0 uppercase tracking-tight">Cấu hình biến thể</h2>
                <p class="text-muted small mb-0">Sản phẩm: <span class="text-primary fw-bold">{{ $product['name'] }}</span></p>
            </div>
            <a href="{{ BASE_URL }}/adminproduct/index" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-arrow-left me-1"></i>QUAY LẠI
            </a>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                @include('admin.variant.them')
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white border border-slate-50">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark border-0">
                                <tr>
                                    <th class="ps-4 py-3" width="80">Ảnh</th>
                                    <th>Chi tiết phiên bản</th>
                                    <th>Giá & SKU</th>
                                    <th>Tồn kho</th>
                                    <th class="text-end pe-4">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($variants as $v)
                                <tr>
                                    <td class="ps-4">
                                        <img src="{{ BASE_URL }}/public/uploads/products/{{ $v['image'] ?: 'default.jpg' }}" 
                                             class="rounded-3 border shadow-sm" width="55" height="55" style="object-fit: cover;"
                                             onerror="this.src='https://placehold.co/100x100?text=No+Img'">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">{{ $v['color_name'] ?: 'Tiêu chuẩn' }}</div>
                                        <div class="badge bg-secondary-subtle text-secondary rounded-pill px-2" style="font-size: 10px;">
                                            {{ $v['size_name'] ?: 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-danger">{{ number_format($v['price']) }}đ</div>
                                        <div class="extra-small text-muted fw-medium">{{ $v['sku'] }}</div>
                                    </td>
                                    <td>
                                        @if($v['stock'] > 10)
                                            <span class="badge bg-success-subtle text-success border-0 px-3 py-1.5 rounded-pill">
                                                <i class="bi bi-box-seam me-1"></i>{{ $v['stock'] }}
                                            </span>
                                        @elseif($v['stock'] > 0)
                                            <span class="badge bg-warning-subtle text-warning border-0 px-3 py-1.5 rounded-pill">
                                                <i class="bi bi-exclamation-circle me-1"></i>CÒN {{ $v['stock'] }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border-0 px-3 py-1.5 rounded-pill">
                                                <i class="bi bi-x-circle me-1"></i>HẾT HÀNG
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group shadow-sm rounded-3">
                                            <button class="btn btn-sm btn-white border btn-edit-variant" 
                                                    data-bs-toggle="modal" data-bs-target="#editVariantModal"
                                                    data-id="{{ $v['id'] }}"
                                                    data-sku="{{ $v['sku'] }}"
                                                    data-color="{{ $v['color_id'] }}"
                                                    data-size="{{ $v['size_id'] }}"
                                                    data-price="{{ $v['price'] }}"
                                                    data-stock="{{ $v['stock'] }}"
                                                    data-image="{{ $v['image'] }}"
                                                    data-productid="{{ $product['id'] }}">
                                                <i class="bi bi-pencil-square text-warning"></i>
                                            </button>
                                            <a href="{{ BASE_URL }}/adminvariant/destroy/{{ $v['id'] }}/{{ $product['id'] }}" 
                                               class="btn btn-sm btn-white border text-danger" 
                                               onclick="return confirm('Gỡ bỏ phiên bản này?')">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center p-5 text-muted italic">Chưa có biến thể nào.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($product)
    @include('admin.variant.edit')
@endif

<style>
    .extra-small { font-size: 11px; }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

@include('admin.layouts.footer')