@include('admin.layouts.header')

<div class="container mt-4 text-dark">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center rounded-4 animate-slide-down">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center rounded-4 animate-slide-down">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['error'] }}</div>
            @php unset($_SESSION['error']) @endphp
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
                        <th class="ps-4 py-3" width="60">STT</th>
                        <th>Thông tin thành viên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th class="text-end pe-4" width="220">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $u)
                    <tr>
                        <td class="ps-4 text-muted">{{ ($currentPage - 1) * 10 + $index + 1 }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($u['fullname']) }}&background=random&color=fff" 
                                     class="rounded-circle me-3 border shadow-sm" width="40" height="40">
                                <div>
                                    <div class="fw-bold text-dark">{{ $u['fullname'] }}</div>
                                    <div class="extra-small text-muted">ID: #{{ $u['id'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="fw-medium text-dark">{{ $u['email'] }}</span></td>
                        <td>
                            @if($u['role'] === 'admin')
                                <span class="badge bg-danger-subtle text-danger border-0 px-3 rounded-pill fw-bold">Admin</span>
                            @else
                                <span class="badge bg-info-subtle text-info border-0 px-3 rounded-pill fw-bold">User</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden">
                                <button class="btn btn-sm btn-white border btn-edit-user"
                                    data-bs-toggle="modal" data-bs-target="#editUserModal"
                                    data-id="{{ $u['id'] }}"
                                    data-fullname="{{ htmlspecialchars($u['fullname']) }}"
                                    data-email="{{ $u['email'] }}"
                                    data-role="{{ $u['role'] }}"
                                    title="Sửa thông tin">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-white border btn-pass-user"
                                    data-bs-toggle="modal" data-bs-target="#passwordUserModal"
                                    data-id="{{ $u['id'] }}"
                                    data-fullname="{{ htmlspecialchars($u['fullname']) }}"
                                    title="Đổi mật khẩu">
                                    <i class="bi bi-key text-info"></i>
                                </button>

                                <a href="{{ rtrim(BASE_URL, '/') }}/adminaddress/index/{{ $u['id'] }}" 
                                   class="btn btn-sm btn-white border" title="Quản lý địa chỉ">
                                    <i class="bi bi-geo-alt text-success"></i>
                                </a>

                                <a href="{{ rtrim(BASE_URL, '/') }}/adminuser/destroy/{{ $u['id'] }}"
                                   class="btn btn-sm btn-white border"
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa thành viên {{ $u['fullname'] }}?')"
                                   title="Xóa thành viên">
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

<div class="modal fade" id="passwordUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-info text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-shield-lock-fill me-2"></i>Đổi mật khẩu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="passwordUserForm" method="POST">
                <div class="modal-body p-4">
                    <p class="text-muted mb-3">Đổi mật khẩu cho: <strong id="pass_fullname_display" class="text-dark"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Mật khẩu mới <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control rounded-3 py-2" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" class="form-control rounded-3 py-2" required minlength="6">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-info text-white rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
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

        const passButtons = document.querySelectorAll('.btn-pass-user');
        const passForm = document.getElementById('passwordUserForm');
        const nameDisplay = document.getElementById('pass_fullname_display');

        passButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                if(passForm) {
                    passForm.action = '{{ rtrim(BASE_URL, "/") }}/adminuser/updatePassword/' + id;
                }
                if(nameDisplay) {
                    nameDisplay.textContent = this.dataset.fullname;
                }
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