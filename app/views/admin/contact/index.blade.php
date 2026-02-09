@include('admin.layouts.header')

<div class="bg-light min-vh-100 py-5">
    <div class="container text-start">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="mb-4 text-center">
                    <h1 class="h3 fw-bold mb-2">Quản Lý Danh Sách Liên Hệ</h1>
                    <p class="text-muted">Xem và quản lý các tin nhắn từ khách hàng gửi qua biểu mẫu liên hệ.</p>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="row align-items-center g-3">
                            <div class="col-md-6">
                                <h5 class="card-title mb-1">
                                    <i class="bi bi-chat-dots text-primary me-2"></i>Tổng số liên hệ: <span class="fw-bold">{{ $totalAll }}</span>
                                </h5>
                                @if(!empty($q))
                                    <p class="small text-muted mb-0">Kết quả tìm kiếm cho "{{ $q }}": <span class="text-primary fw-bold">{{ $totalFiltered }}</span></p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <form action="" method="GET">
                                    <input type="hidden" name="act" value="admincontact/index">
                                    <div class="input-group">
                                        <input type="text" name="q" class="form-control" placeholder="Tìm tên, email hoặc tiêu đề..." value="{{ $q }}">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="bi bi-search"></i> Tìm kiếm
                                        </button>
                                        @if(!empty($q))
                                            <a href="{{ rtrim(BASE_URL, '/') }}/admincontact/index" class="btn btn-outline-secondary">Xóa lọc</a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-white border-bottom">
                                <tr>
                                    <th class="ps-4 py-3 text-uppercase small fw-bold text-muted" width="80">ID</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Khách hàng</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted">Nội dung liên hệ</th>
                                    <th class="py-3 text-uppercase small fw-bold text-muted text-center">Ngày gửi</th>
                                    <th class="pe-4 py-3 text-uppercase small fw-bold text-muted text-end">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @forelse ($contacts as $c)
                                <tr>
                                    <td class="ps-4 fw-medium text-muted">#{{ $c['id'] }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $c['full_name'] }}</div>
                                        <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $c['email'] }}</div>
                                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $c['phone'] }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary small mb-1">{{ $c['subject'] }}</div>
                                        <div class="small text-dark text-truncate" style="max-width: 350px;" title="{{ $c['message'] }}">
                                            {{ $c['message'] }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="small text-dark d-block fw-medium">{{ date('d/m/Y', strtotime($c['created_at'])) }}</span>
                                        <span class="extra-small text-muted">{{ date('H:i', strtotime($c['created_at'])) }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <form action="{{ rtrim(BASE_URL, '/') }}/admincontact/delete?q={{ $q }}&page={{ $currentPage }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa liên hệ này?')">
                                            <input type="hidden" name="id" value="{{ $c['id'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-3">
                                                <i class="bi bi-trash3 me-1"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted italic">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                        Không có dữ liệu liên hệ nào được tìm thấy.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($totalPages > 1)
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        @for ($i = 1; $i <= $totalPages; $i++)
                            <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                                <a class="page-link border-0 shadow-sm mx-1 rounded-3 {{ ($currentPage == $i) ? 'bg-primary text-white' : 'bg-white text-dark' }}" 
                                   href="?act=admincontact/index&page={{ $i }}&q={{ urlencode($q) }}">{{ $i }}</a>
                            </li>
                        @endfor
                    </ul>
                </nav>
                @endif

                <div class="row g-3 mt-5">
                    <div class="col-md-4">
                        <div class="card text-center h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="mb-2 text-primary"><i class="bi bi-envelope fs-3"></i></div>
                                <h6 class="fw-bold">Email hỗ trợ</h6>
                                <p class="small text-muted mb-0">contact@simpleshop.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="mb-2 text-primary"><i class="bi bi-telephone fs-3"></i></div>
                                <h6 class="fw-bold">Hotline</h6>
                                <p class="small text-muted mb-0">+84 123 456 789</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center h-100 shadow-sm border-0">
                            <div class="card-body">
                                <div class="mb-2 text-primary"><i class="bi bi-geo-alt fs-3"></i></div>
                                <h6 class="fw-bold">Địa chỉ</h6>
                                <p class="small text-muted mb-0">123 Street, District 1, HCM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.layouts.footer')