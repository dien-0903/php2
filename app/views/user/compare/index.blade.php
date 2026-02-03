@php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
@endphp

@include('user.layouts.header')

<div class="container py-5 text-dark">
    <div class="mb-5 text-center">
        <h1 class="fw-black uppercase text-slate-900 tracking-tighter mb-2">So sánh sản phẩm</h1>
        <p class="text-muted">Dễ dàng chọn lựa sản phẩm phù hợp nhất (Tối đa 3)</p>
    </div>

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 text-center">{{ $_SESSION['error'] }}</div>
        @php unset($_SESSION['error']) @endphp
    @endif

    @if(empty($products) && empty($_SESSION['compare']))
        <div class="text-center py-5 bg-white rounded-5 shadow-sm border border-dashed">
            <i class="bi bi-layers display-1 text-slate-200 mb-3 d-block"></i>
            <h5 class="fw-bold text-secondary">Chưa có sản phẩm nào để so sánh!</h5>
            <a href="{{ rtrim(BASE_URL, '/') }}/product/index" class="btn btn-primary rounded-pill mt-3">Thêm sản phẩm</a>
        </div>
    @else
        <!-- Nếu chưa có hàm getByIds trong Model thì layout này sẽ rỗng data, cần bổ sung Model -->
        <div class="table-responsive bg-white rounded-5 shadow-sm p-4 border border-slate-50">
            @if(empty($products))
                <div class="alert alert-warning text-center">Vui lòng cập nhật Model (getByIds) để hiển thị dữ liệu chi tiết. ID đang so sánh: {{ implode(', ', $_SESSION['compare']) }}</div>
            @else
            <table class="table table-bordered text-center align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3" style="width: 20%;">Tiêu chí</th>
                        @foreach($products as $p)
                            <th class="py-3" style="width: 26%;">
                                <div class="position-relative">
                                    <a href="{{ rtrim(BASE_URL, '/') }}/compare/remove/{{ $p['id'] }}" class="position-absolute top-0 end-0 text-danger" title="Xóa"><i class="bi bi-x-circle-fill"></i></a>
                                    <h6 class="fw-bold mb-0 text-truncate px-3">{{ $p['name'] }}</h6>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold text-secondary bg-light">Hình ảnh</td>
                        @foreach($products as $p)
                            <td class="py-4">
                                @php        
                                            $APP_URL = 'http://localhost/PHP2';
                                            $imagePath = $APP_URL . '/public/uploads/products/' . ($p['image'] ?: 'default.jpg');
                                        @endphp
                                        <img src="{{ $imagePath }}" 
                                             class="rounded shadow-sm border transition-transform group-hover:scale-105" 
                                             width="160" height="160" style="object-fit: cover;"
                                             onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="fw-bold text-secondary bg-light">Giá bán</td>
                        @foreach($products as $p)
                            <td class="fw-black text-rose-600 fs-5">{{ number_format($p['price'], 0, ',', '.') }}đ</td>
                        @endforeach
                    </tr>
                     <tr>
                        <td class="fw-bold text-secondary bg-light">Thương hiệu</td>
                        @foreach($products as $p)
                            <td>{{ $p['brand_name'] ?? 'N/A' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="fw-bold text-secondary bg-light">Hành động</td>
                        @foreach($products as $p)
                            <td>
                                <a href="{{ rtrim(BASE_URL, '/cart/index') }}/cart/add/{{ $p['id'] }}" class="btn btn-primary rounded-pill btn-sm px-3 fw-bold">Thêm giỏ hàng</a>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
            @endif
        </div>
    @endif
</div>

@include('user.layouts.footer')