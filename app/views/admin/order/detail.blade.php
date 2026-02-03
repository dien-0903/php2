@include('admin.layouts.header')

<div class="container mt-4 text-dark mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ rtrim(BASE_URL, '/') }}/adminorder/index" class="text-decoration-none">Đơn hàng</a></li>
                <li class="breadcrumb-item active" aria-current="page">Chi tiết #{{ $order['order_code'] }}</li>
            </ol>
        </nav>
        <a href="{{ rtrim(BASE_URL, '/') }}/adminorder/index" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 animate-slide-down">
            <i class="bi bi-check-circle-fill me-2"></i> {{ $_SESSION['success'] }}
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    <div class="row g-4">
        <!-- BÊN TRÁI: THÔNG TIN SẢN PHẨM -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam text-primary me-2"></i>Sản phẩm trong đơn</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 border-0 small text-muted text-uppercase" width="80">Ảnh</th>
                                    <th class="py-3 border-0 small text-muted text-uppercase">Tên sản phẩm</th>
                                    <th class="py-3 border-0 small text-muted text-uppercase text-center">Giá</th>
                                    <th class="py-3 border-0 small text-muted text-uppercase text-center">SL</th>
                                    <th class="pe-4 py-3 border-0 small text-muted text-uppercase text-end">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td class="ps-4 py-3">
                                        @php $imgUrl = rtrim(BASE_URL, '/') . '/public/uploads/products/' . ($item['product_image'] ?: 'default.jpg'); @endphp
                                        <img src="http://localhost/PHP2/public/uploads/products/{{ $item['product_image'] }}" class="rounded border" width="60" height="60" style="object-fit: cover;">
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item['product_name'] }}</div>
                                        <div class="extra-small text-muted italic">{{ $item['variant_info'] ?: 'Mặc định' }}</div>
                                    </td>
                                    <td class="text-center">{{ number_format($item['price']) }}đ</td>
                                    <td class="text-center fw-bold text-primary">x{{ $item['quantity'] }}</td>
                                    <td class="pe-4 text-end fw-bold text-danger">{{ number_format($item['price'] * $item['quantity']) }}đ</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white p-4 border-top">
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tổng giá trị sản phẩm:</span>
                                <span class="fw-bold">{{ number_format($order['total_amount']) }}đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Phí vận chuyển:</span>
                                <span class="fw-bold text-success">Miễn phí</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="h5 fw-bold mb-0">TỔNG CỘNG:</span>
                                <span class="h5 fw-black text-danger mb-0">{{ number_format($order['total_amount']) }}đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-muted text-uppercase mb-3"><i class="bi bi-chat-left-dots me-2"></i>Ghi chú từ khách hàng</h6>
                    <div class="p-3 bg-light rounded-3 italic">
                        {{ $order['note'] ?: 'Không có ghi chú nào.' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- BÊN PHẢI: TRẠNG THÁI & NGƯỜI NHẬN -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                <div class="card-header bg-primary text-white py-3 px-4 rounded-top-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-gear-fill me-2"></i>Trạng thái đơn hàng</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ rtrim(BASE_URL, '/') }}/adminorder/updateStatus" method="POST">
                        <input type="hidden" name="order_id" value="{{ $order['id'] }}">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase">Thay đổi trạng thái:</label>
                            <select name="status" class="form-select rounded-3 border-primary shadow-none py-2 fw-bold">
                                <option value="0" {{ $order['status'] == 0 ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="1" {{ $order['status'] == 1 ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="2" {{ $order['status'] == 2 ? 'selected' : '' }}>Đang giao hàng</option>
                                <option value="3" {{ $order['status'] == 3 ? 'selected' : '' }}>Hoàn thành</option>
                                <option value="4" {{ $order['status'] == 4 ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                            CẬP NHẬT TRẠNG THÁI
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Người nhận hàng</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="small text-muted d-block uppercase fw-bold mb-1">Họ tên:</label>
                        <div class="fw-bold text-dark">{{ $order['recipient_name'] }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block uppercase fw-bold mb-1">Số điện thoại:</label>
                        <div class="fw-bold"><i class="bi bi-telephone text-primary me-2"></i>{{ $order['phone'] }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted d-block uppercase fw-bold mb-1">Địa chỉ giao hàng:</label>
                        <div class="small lh-base fw-medium text-dark">
                            <i class="bi bi-geo-alt text-danger me-2"></i>{{ $order['address'] }}
                        </div>
                    </div>
                    <div class="pt-3 border-top">
                        <label class="small text-muted d-block uppercase fw-bold mb-1">Phương thức thanh toán:</label>
                        <span class="badge bg-dark rounded-pill px-3 py-2">
                            @if($order['payment_method'] == 'cod')
                                <i class="bi bi-cash me-1 text-warning"></i> Thanh toán COD
                            @else
                                <i class="bi bi-credit-card me-1 text-info"></i> Chuyển khoản
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('admin.layouts.footer')