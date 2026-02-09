@include('user.layouts.header')

<div class="container py-5 text-dark min-vh-100">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white animate-slide-down">
                <div class="text-center mb-4 pt-3">
                    <div class="position-relative d-inline-block mb-3">
                        @php
                            $avatarPath = !empty($user['avatar']) 
                                ? rtrim(BASE_URL, '/') . '/public/uploads/users/' . $user['avatar'] 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($user['fullname']) . '&background=0d6efd&color=fff&size=128';
                        @endphp
                        <img id="sidebar-avatar-preview" src="{{ $avatarPath }}" 
                             class="rounded-circle border border-4 border-light shadow-sm" 
                             width="120" height="120" style="object-fit: cover;" alt="Avatar">
                        
                        <label for="avatar-input" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 cursor-pointer shadow-sm border border-2 border-white" title="Đổi ảnh đại diện">
                            <i class="bi bi-camera-fill" style="font-size: 0.8rem;"></i>
                        </label>
                    </div>
                    <h5 class="fw-bold mb-0 text-truncate px-2">{{ $user['fullname'] }}</h5>
                    <p class="text-muted small mb-0">{{ $user['email'] }}</p>
                    <div class="mt-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill extra-small fw-bold px-3 uppercase">Thành viên MD</span>
                    </div>
                </div>
                
                <div class="list-group list-group-flush rounded-3 overflow-hidden border-top">
                    <a href="{{ rtrim(BASE_URL, '/') }}/user/profile" class="list-group-item list-group-item-action active border-0 py-3">
                        <i class="bi bi-person-bounding-box me-2"></i> Hồ sơ cá nhân
                    </a>
                    <a href="{{ rtrim(BASE_URL, '/') }}/order/history" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="bi bi-bag-check me-2"></i> Lịch sử đơn hàng
                    </a>
                    <a href="{{ rtrim(BASE_URL, '/') }}/user/address" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="bi bi-geo-alt me-2"></i> Sổ địa chỉ
                    </a>
                    <hr class="my-1 opacity-10">
                    <a href="{{ rtrim(BASE_URL, '/') }}/auth/logout" class="list-group-item list-group-item-action border-0 py-3 text-danger fw-bold">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4 transition-all">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary text-white rounded-3 p-2 me-3 shadow-sm">
                        <i class="bi bi-person-gear fs-5"></i>
                    </div>
                    <h5 class="fw-black mb-0 uppercase tracking-tight">Thông tin tài khoản</h5>
                </div>
                
                <form action="{{ rtrim(BASE_URL, '/') }}/user/updateProfile" method="POST" enctype="multipart/form-data">
                    <input type="file" name="avatar" id="avatar-input" class="d-none" accept="image/*" onchange="previewAvatar(this)">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary uppercase">Họ và tên</label>
                            <input type="text" name="fullname" class="form-control rounded-3 py-2 bg-light border-0 shadow-none focus-ring" 
                                   value="{{ $user['fullname'] }}" required placeholder="Ví dụ: Nguyễn Văn A">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary uppercase">Địa chỉ Email</label>
                            <input type="email" name="email" class="form-control rounded-3 py-2 bg-light border-0 shadow-none focus-ring" 
                                   value="{{ $user['email'] }}" required placeholder="email@example.com">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary uppercase">Số điện thoại</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 text-muted rounded-start-3"><i class="bi bi-phone"></i></span>
                                <input type="text" name="phone" class="form-control rounded-end-3 py-2 bg-light border-0 shadow-none focus-ring" 
                                       value="{{ $user['phone'] ?? '' }}" placeholder="Ví dụ: 0912xxxxxx">
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm py-2">
                                <i class="bi bi-check2-circle me-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white shadow-hover">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-warning text-dark rounded-3 p-2 me-3 shadow-sm">
                        <i class="bi bi-shield-lock fs-5"></i>
                    </div>
                    <h5 class="fw-black mb-0 uppercase tracking-tight">Thay đổi mật khẩu</h5>
                </div>
                
                <form action="{{ rtrim(BASE_URL, '/') }}/user/changePassword" method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-secondary uppercase">Mật khẩu hiện tại</label>
                            <div class="position-relative">
                                <input type="password" name="old_password" class="form-control rounded-3 py-2 bg-light border-0 shadow-none focus-ring pe-5" 
                                       required placeholder="Nhập mật khẩu hiện tại">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-3 toggle-password-btn">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary uppercase">Mật khẩu mới</label>
                            <div class="position-relative">
                                <input type="password" name="new_password" class="form-control rounded-3 py-2 bg-light border-0 shadow-none focus-ring pe-5" 
                                       required minlength="6" placeholder="Tối thiểu 6 ký tự">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-3 toggle-password-btn">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-secondary uppercase">Xác nhận mật khẩu mới</label>
                            <div class="position-relative">
                                <input type="password" name="confirm_password" class="form-control rounded-3 py-2 bg-light border-0 shadow-none focus-ring pe-5" 
                                       required placeholder="Nhập lại mật khẩu mới">
                                <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary text-decoration-none pe-3 toggle-password-btn">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-2">
                            <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm py-2 text-dark">
                                <i class="bi bi-key-fill me-1"></i> Cập nhật mật khẩu
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .extra-small { font-size: 10px; }
    .cursor-pointer { cursor: pointer; }
    
    .list-group-item { transition: 0.2s; font-size: 0.9rem; font-weight: 500; color: #64748b; }
    .list-group-item:hover { background-color: #f8fafc; color: #2563eb; padding-left: 25px !important; }
    .list-group-item.active { background-color: #2563eb !important; border-color: #2563eb !important; color: #fff !important; }
    
    .bg-light { background-color: #f1f5f9 !important; }
    .focus-ring:focus { background-color: #fff !important; border: 1px solid #2563eb !important; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important; }
    
    .animate-slide-down { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown { 
        from { transform: translateY(-15px); opacity: 0; } 
        to { transform: translateY(0); opacity: 1; } 
    }
    .toggle-password-btn:hover { color: #2563eb !important; }
</style>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('sidebar-avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtns = document.querySelectorAll('.toggle-password-btn');
        
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });
    });
</script>

@include('user.layouts.footer')