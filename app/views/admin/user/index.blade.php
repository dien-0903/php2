@include('admin.layouts.header')

<div class="container mt-4 text-dark">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center rounded-4 animate-slide-down">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold uppercase">
            <i class="bi bi-people-fill text-primary me-2"></i>Quản lý Thành viên
        </h2>
        <button class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus me-1"></i>Thêm thành viên mới
        </button>
    </div>

    <div class="card p-3 mb-4 shadow-sm border-0 rounded-4 bg-white border border-slate-100">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminuser/index" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 shadow-none rounded-end-pill py-2"
                        placeholder="Tìm tên hoặc email..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill text-white">TÌM KIẾM</button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ rtrim(BASE_URL, '/') }}/adminuser/index" class="btn btn-outline-secondary w-100 rounded-pill">XÓA LỌC</a>
                </div>
            @endif
        </form>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white border border-slate-50">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark border-0">
                    <tr>
                        <th class="ps-4 py-3" width="80">STT</th>
                        <th>Thông tin thành viên</th>
                        <th>Địa chỉ Email</th>
                        <th>Vai trò</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $u)
                    <tr>
                        <td class="ps-4 text-muted">{{ ($currentPage - 1) * 10 + $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($u['fullname']) }}&background=random&color=fff" 
                                     class="rounded-circle me-3 border shadow-sm" width="45" height="45">
                                <div>
                                    <div class="fw-bold text-dark">{{ $u['fullname'] }}</div>
                                    <div class="extra-small text-muted">ID: #{{ $u['id'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="fw-medium text-dark">{{ $u['email'] }}</span></td>
                        <td>
                            @if($u['role'] === 'admin')
                                <span class="badge bg-danger-subtle text-danger border-0 px-3 rounded-pill fw-bold">QUẢN TRỊ</span>
                            @else
                                <span class="badge bg-info-subtle text-info border-0 px-3 rounded-pill fw-bold">NGƯỜI DÙNG</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                <button class="btn btn-sm btn-white border btn-edit-user"
                                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                                    data-id="{{ $u['id'] }}"
                                    data-fullname="{{ htmlspecialchars($u['fullname']) }}"
                                    data-email="{{ $u['email'] }}"
                                    data-role="{{ $u['role'] }}">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>
                                <a href="{{ rtrim(BASE_URL, '/') }}/adminuser/destroy/{{ $u['id'] }}"
                                   class="btn btn-sm btn-white border"
                                   onclick="return confirm('Bạn có chắc chắn muốn gỡ bỏ thành viên {{ $u['fullname'] }}?')">
                                    <i class="bi bi-trash text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center p-5 text-muted">Chưa có thành viên nào trong danh sách.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (isset($totalPages) && $totalPages > 1)
    <nav class="mt-4 mb-5">
        <ul class="pagination justify-content-center gap-2">
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark' }}" 
                       href="{{ rtrim(BASE_URL, '/') }}/adminuser/index?page={{ $i }}&search={{ urlencode($search ?? '') }}">{{ $i }}</a>
                </li>
            @endfor
        </ul>
    </nav>
    @endif
</div>

@include('admin.user.them')
@include('admin.user.edit')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-user');
        const editForm = document.getElementById('editUserForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                if(editForm) {
                    editForm.action = '{{ rtrim(BASE_URL, "/") }}/adminuser/update/' + id;
                }

                document.getElementById('edit_fullname').value = this.dataset.fullname;
                document.getElementById('edit_email').value = this.dataset.email;
                document.getElementById('edit_role').value = this.dataset.role;
            });
        });

        @if(isset($_SESSION['error_type']))
            const modalId = "{{ $_SESSION['error_type'] === 'add' ? '#addUserModal' : '#editUserModal' }}";
            const targetModal = document.querySelector(modalId);
            if(targetModal) {
                const bootstrapModal = new bootstrap.Modal(targetModal);
                bootstrapModal.show();
            }
        @endif
    });
</script>

<style>
    .extra-small { font-size: 11px; }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

@include('admin.layouts.footer')