@include('admin.layouts.header')

<div class="container mt-4 text-dark mb-5 animate-fade-in">
    <!-- Tiêu đề trang -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold uppercase mb-0 tracking-tighter">
            <i class="bi bi-cart-check-fill text-primary me-2"></i>Quản lý đơn hàng
        </h2>
        <div class="text-muted small fw-medium">
            Hiển thị trang {{ $currentPage ?? 1 }} / {{ $totalPages ?? 1 }} (Tổng {{ $totalCount ?? count($orders) }} đơn hàng)
        </div>
    </div>

    <!-- BỘ LỌC CHUẨN THEO THIẾT KẾ -->
    <div class="card p-4 mb-5 shadow-sm border-0 rounded-4 bg-white border border-slate-100">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminorder/index" method="GET" class="row g-4 align-items-end">
            <!-- Trạng thái -->
            <div class="col-md-3 text-start">
                <label class="form-label extra-small fw-bold text-muted text-uppercase tracking-wider mb-2 ms-2">TRẠNG THÁI</label>
                <div class="rounded-pill border px-2 bg-light shadow-inner">
                    <select name="status" class="form-select border-0 shadow-none py-2.5 fw-bold text-secondary bg-transparent cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        <option value="0" {{ (isset($status) && $status === '0') ? 'selected' : '' }}>🕒 Chờ xử lý</option>
                        <option value="1" {{ (isset($status) && $status === '1') ? 'selected' : '' }}>✅ Đã xác nhận</option>
                        <option value="2" {{ (isset($status) && $status === '2') ? 'selected' : '' }}>🚚 Đang giao</option>
                        <option value="3" {{ (isset($status) && $status === '3') ? 'selected' : '' }}>🎉 Hoàn thành</option>
                        <option value="4" {{ (isset($status) && $status === '4') ? 'selected' : '' }}>❌ Đã hủy</option>
                    </select>
                </div>
            </div>
            
            <!-- Tìm kiếm -->
            <div class="col-md-5 text-start">
                <label class="form-label extra-small fw-bold text-muted text-uppercase tracking-wider mb-2 ms-2">TÌM KIẾM</label>
                <div class="input-group rounded-pill border overflow-hidden bg-light shadow-inner">
                    <span class="input-group-text bg-transparent border-0 ps-3 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none py-2.5 ps-2" 
                           placeholder="Mã đơn hàng hoặc tên khách..." value="{{ $search ?? '' }}">
                </div>
            </div>
            
            <!-- Nút bấm -->
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2.5 shadow-sm text-uppercase transition-all hover-lift">
                        LỌC DỮ LIỆU
                    </button>
                    <a href="{{ rtrim(BASE_URL, '/') }}/adminorder/index" class="btn btn-outline-secondary rounded-pill py-2.5 px-4 text-uppercase transition-all hover-lift">
                        XÓA LỌC
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bảng Đơn hàng -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white border border-slate-100">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark border-0">
                    <tr>
                        <th class="ps-4 py-3" width="160">Mã đơn</th>
                        <th>Thông tin người nhận</th>
                        <th class="text-center">Ngày đặt</th>
                        <th class="text-center">Tổng tiền</th>
                        <th class="text-center" width="220">Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $o)
                    <tr id="order-row-{{ $o['id'] }}">
                        <td class="ps-4">
                            <a href="{{ rtrim(BASE_URL, '/') }}/adminorder/show/{{ $o['id'] }}" class="fw-bold text-primary text-decoration-none hover-underline">
                                #{{ $o['order_code'] }}
                            </a>
                        </td>
                        <td class="text-start">
                            <div class="fw-bold text-dark">{{ $o['recipient_name'] }}</div>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $o['phone'] }}</div>
                        </td>
                        <td class="text-center small">
                            <div class="fw-medium">{{ date('d/m/Y', strtotime($o['created_at'])) }}</div>
                            <div class="extra-small text-muted">{{ date('H:i', strtotime($o['created_at'])) }}</div>
                        </td>
                        <td class="text-center">
                            <span class="fw-black text-danger">{{ number_format($o['total_amount']) }}đ</span>
                        </td>
                        <td class="text-center">
                            <!-- FIX: Sử dụng AJAX thay vì form submit truyền thống để tránh nhảy trang -->
                            <div class="position-relative d-inline-block w-100">
                                <select class="form-select form-select-sm rounded-pill border-0 shadow-sm fw-bold status-ajax-select
                                    {{ $o['status'] == 0 ? 'bg-warning-subtle text-warning' : '' }}
                                    {{ $o['status'] == 1 ? 'bg-info-subtle text-info' : '' }}
                                    {{ $o['status'] == 2 ? 'bg-primary-subtle text-primary' : '' }}
                                    {{ $o['status'] == 3 ? 'bg-success-subtle text-success' : '' }}
                                    {{ $o['status'] == 4 ? 'bg-danger-subtle text-danger' : '' }}" 
                                    data-order-id="{{ $o['id'] }}"
                                    onchange="updateStatusAjax(this)">
                                    <option value="0" {{ $o['status'] == 0 ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="1" {{ $o['status'] == 1 ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="2" {{ $o['status'] == 2 ? 'selected' : '' }}>Đang giao</option>
                                    <option value="3" {{ $o['status'] == 3 ? 'selected' : '' }}>Hoàn thành</option>
                                    <option value="4" {{ $o['status'] == 4 ? 'selected' : '' }}>Đã hủy</option>
                                </select>
                                <div class="spinner-border spinner-border-sm text-primary position-absolute d-none" 
                                     id="loader-{{ $o['id'] }}" 
                                     style="right: -25px; top: 8px;" role="status"></div>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ rtrim(BASE_URL, '/') }}/adminorder/show/{{ $o['id'] }}" 
                               class="btn btn-sm btn-white border shadow-sm rounded-pill px-4 fw-bold transition-all hover-lift">
                                <i class="bi bi-eye-fill me-1 text-primary"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-5 text-muted border-0">
                            <i class="bi bi-inbox display-4 d-block mb-2 opacity-25"></i>
                            Không tìm thấy đơn hàng nào phù hợp.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- KHỐI PHÂN TRANG -->
    <nav class="mt-5 mb-5">
        <ul class="pagination justify-content-center gap-2">
            @if (isset($totalPages) && $totalPages > 1)
                <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white" 
                       href="?page={{ ($currentPage ?? 1) - 1 }}&status={{ $status }}&search={{ urlencode($search ?? '') }}">
                       <i class="bi bi-chevron-left"></i>
                    </a>
                </li>

                @for ($i = 1; $i <= $totalPages; $i++)
                    <li class="page-item {{ (($currentPage ?? 1) == $i) ? 'active' : '' }}">
                        <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold {{ (($currentPage ?? 1) == $i) ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark' }}" 
                           href="?page={{ $i }}&status={{ $status }}&search={{ urlencode($search ?? '') }}">
                            {{ $i }}
                        </a>
                    </li>
                @endfor

                <li class="page-item {{ ($currentPage ?? 1) >= $totalPages ? 'disabled' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white" 
                       href="?page={{ ($currentPage ?? 1) + 1 }}&status={{ $status }}&search={{ urlencode($search ?? '') }}">
                       <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <!-- Luôn hiển thị nút trang 1 để giao diện không bị trống nếu bạn muốn nút luôn xuất hiện -->
                <li class="page-item active">
                    <span class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold bg-primary text-white shadow-primary">1</span>
                </li>
            @endif
        </ul>
    </nav>
</div>

<!-- SCRIPT CẬP NHẬT AJAX -->
<script>
async function updateStatusAjax(selectElement) {
    const orderId = selectElement.dataset.orderId;
    const newStatus = selectElement.value;
    const loader = document.getElementById(`loader-${orderId}`);
    
    // Hiện loader và vô hiệu hóa select tạm thời
    loader.classList.remove('d-none');
    selectElement.disabled = true;

    try {
        const formData = new FormData();
        formData.append('order_id', orderId);
        formData.append('status', newStatus);

        const response = await fetch('{{ rtrim(BASE_URL, "/") }}/adminorder/updateStatus', {
            method: 'POST',
            body: formData
        });

        // Đổi màu nền select tương ứng với trạng thái mới
        selectElement.className = 'form-select form-select-sm rounded-pill border-0 shadow-sm fw-bold status-ajax-select';
        if (newStatus == 0) selectElement.classList.add('bg-warning-subtle', 'text-warning');
        if (newStatus == 1) selectElement.classList.add('bg-info-subtle', 'text-info');
        if (newStatus == 2) selectElement.classList.add('bg-primary-subtle', 'text-primary');
        if (newStatus == 3) selectElement.classList.add('bg-success-subtle', 'text-success');
        if (newStatus == 4) selectElement.classList.add('bg-danger-subtle', 'text-danger');

        // Thông báo thành công nhẹ nhàng (không cần alert gây phiền)
        console.log(`Đã cập nhật đơn #MD-${orderId}`);
        
    } catch (error) {
        console.error('Lỗi cập nhật:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái!');
    } finally {
        // Ẩn loader và kích hoạt lại select
        loader.classList.add('d-none');
        selectElement.disabled = false;
    }
}
</script>

<style>
    .fw-black { font-weight: 900; }
    .extra-small { font-size: 10px; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05); }
    .bg-warning-subtle { background-color: #fff9db !important; color: #f59f00 !important; }
    .bg-info-subtle { background-color: #e7f5ff !important; color: #1c7ed6 !important; }
    .bg-primary-subtle { background-color: #e7f5ff !important; color: #339af0 !important; }
    .bg-success-subtle { background-color: #ebfbee !important; color: #37b24d !important; }
    .bg-danger-subtle { background-color: #fff5f5 !important; color: #f03e3e !important; }
    .btn-white:hover { background-color: #f8fafc; }
    .hover-underline:hover { text-decoration: underline !important; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
    .transition-all { transition: all 0.3s ease; }
    .cursor-pointer { cursor: pointer; }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    .animate-fade-in { animation: fadeIn 0.5s ease-in; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>

@include('admin.layouts.footer')