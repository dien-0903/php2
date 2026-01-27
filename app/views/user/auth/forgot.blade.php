@include('user.layouts.header')

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5 col-lg-4">
            <div class="auth-card shadow-lg border-0 rounded-4 overflow-hidden" style="background: #1e293b;">
                
                <div class="auth-header text-center py-5" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff;">
                    <i class="bi bi-shield-lock-fill" style="font-size: 3.5rem; display: block; margin-bottom: 10px;"></i>
                    <h3 class="fw-bold mb-0 uppercase tracking-tighter">ĐẶT LẠI MẬT KHẨU</h3>
                    <p class="small opacity-75 mt-1">Cập nhật mật khẩu mới cho tài khoản của bạn</p>
                </div>

                <div class="card-body p-4 p-md-5 text-dark">
                    <div id="auth-alert" class="alert d-none border-0 shadow-sm mb-4 py-3 animate-slide-in">
                        <div class="d-flex align-items-center">
                            <i id="alert-icon" class="bi me-3 fs-4"></i>
                            <span id="alert-message" class="fw-bold small"></span>
                        </div>
                    </div>

                    <form id="forgotForm">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-slate-400">Email của bạn</label>
                            <div class="input-group">
                                <span class="input-group-text border-0" style="background-color: #334155; color: #94a3b8;">
                                    <i class="bi bi-envelope-at"></i>
                                </span>
                                <input type="email" name="email" class="form-control border-0 text-white shadow-none" 
                                       style="background-color: #334155; padding: 12px;" 
                                       placeholder="Email đã đăng ký..." required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase text-slate-400">Mật khẩu mới</label>
                            <div class="input-group">
                                <span class="input-group-text border-0" style="background-color: #334155; color: #94a3b8;">
                                    <i class="bi bi-key-fill"></i>
                                </span>
                                <input type="password" name="password" class="form-control border-0 text-white shadow-none" 
                                       style="background-color: #334155; padding: 12px;" 
                                       placeholder="Tối thiểu 6 ký tự..." required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase text-slate-400">Xác nhận lại</label>
                            <div class="input-group">
                                <span class="input-group-text border-0" style="background-color: #334155; color: #94a3b8;">
                                    <i class="bi bi-check-all"></i>
                                </span>
                                <input type="password" name="confirm_password" class="form-control border-0 text-white shadow-none" 
                                       style="background-color: #334155; padding: 12px;" 
                                       placeholder="Nhập lại mật khẩu..." required>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-reset w-100 shadow-sm mb-4 fw-bold uppercase" 
                                style="background-color: #f59e0b; color: #fff; border: none; padding: 12px; border-radius: 12px; transition: 0.3s;">
                            <span id="btnText">XÁC NHẬN ĐỔI MẬT KHẨU</span>
                            <div id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>

                        <div class="text-center">
                            <a href="{{ BASE_URL }}/auth/login" class="text-decoration-none small fw-bold" style="color: #f59e0b;">
                                <i class="bi bi-arrow-left me-1"></i> Quay lại Đăng nhập
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('forgotForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const alertBox = document.getElementById('auth-alert');
    const alertMsg = document.getElementById('alert-message');
    const alertIcon = document.getElementById('alert-icon');
    
    btn.disabled = true;
    btnText.innerText = 'ĐANG CẬP NHẬT...';
    btnLoader.classList.remove('d-none');
    alertBox.classList.add('d-none');

    try {
        const formData = new FormData(form);
        const response = await fetch('{{ BASE_URL }}/auth/postForgot', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) throw new Error('Network response was not ok');
        
        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success border-0 bg-success bg-opacity-10 text-success small mb-4 py-3 d-block';
            alertIcon.className = 'bi bi-check-circle-fill fs-4';
            alertMsg.innerText = result.message;
            
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1500);
        } else {
            alertBox.className = 'alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small mb-4 py-3 d-block animate-shake';
            alertIcon.className = 'bi bi-exclamation-triangle-fill fs-4';
            alertMsg.innerText = result.message;
            
            btn.disabled = false;
            btnText.innerText = 'XÁC NHẬN ĐỔI MẬT KHẨU';
            btnLoader.classList.add('d-none');
        }
    } catch (error) {
        console.error('Lỗi chi tiết:', error);
        alertBox.className = 'alert alert-danger border-0 bg-danger bg-opacity-10 text-danger small mb-4 py-3 d-block';
        alertMsg.innerText = 'Lỗi xử lý dữ liệu từ máy chủ!';
        btn.disabled = false;
        btnText.innerText = 'XÁC NHẬN ĐỔI MẬT KHẨU';
        btnLoader.classList.add('d-none');
    }
});
</script>

<style>
    body { background-color: #0f172a !important; }
    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .btn-reset:hover { background-color: #d97706 !important; transform: translateY(-2px); }
</style>

@include('user.layouts.footer')