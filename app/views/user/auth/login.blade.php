@include('user.layouts.header')

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 75vh;">
        <div class="col-md-5 col-lg-4 text-dark">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                <div class="bg-dark p-5 text-center text-white" style="background-color: #0f172a !important; border-bottom: 3px solid #2563eb;">
                    <i class="bi bi-person-circle display-1 text-primary"></i>
                    <h2 class="fw-black mt-3 uppercase tracking-tighter">ĐĂNG NHẬP</h2>
                    <p class="text-white-50 small mb-0">Chào mừng bạn quay trở lại MD</p>
                </div>

                <div class="card-body p-5 bg-white">
                    <div id="auth-alert" class="alert d-none border-0 shadow-sm mb-4 py-3 animate-slide-in">
                        <div class="d-flex align-items-center">
                            <i id="alert-icon" class="bi me-3 fs-4"></i>
                            <span id="alert-message" class="fw-bold small"></span>
                        </div>
                    </div>

                    <form id="loginForm">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small uppercase tracking-wider">Tài khoản Email</label>
                            <div class="input-group bg-light rounded-3 overflow-hidden border">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-envelope-at text-muted"></i></span>
                                <input type="email" name="email" class="form-control bg-transparent border-0 py-3 shadow-none" 
                                       placeholder="name@example.com" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-bold text-secondary small uppercase tracking-wider mb-0">Mật khẩu</label>
                                <a href="{{ rtrim(BASE_URL, '/') }}/auth/forgot" class="forgot-link">Quên mật khẩu?</a>
                            </div>
                            <div class="input-group bg-light rounded-3 overflow-hidden border">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="password" class="form-control bg-transparent border-0 py-3 shadow-none" 
                                       placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg mb-4 border-0 transition-all">
                            <span id="btnText">XÁC NHẬN ĐĂNG NHẬP</span>
                            <div id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>

                        <div class="text-center">
                            <p class="text-muted small mb-0">
                                Chưa có tài khoản? <a href="{{ rtrim(BASE_URL, '/') }}/auth/register" class="text-primary fw-bold text-decoration-none">Đăng ký thành viên</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const alertBox = document.getElementById('auth-alert');
    const alertMsg = document.getElementById('alert-message');
    const alertIcon = document.getElementById('alert-icon');
    
    btn.disabled = true;
    btnText.innerText = 'ĐANG XỬ LÝ...';
    btnLoader.classList.remove('d-none');
    alertBox.classList.add('d-none');

    try {
        const formData = new FormData(form);
        const response = await fetch('{{ rtrim(BASE_URL, "/") }}/auth/postLogin', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success border-0 shadow-sm mb-4 py-3 d-block';
            alertIcon.className = 'bi bi-check-circle-fill fs-4';
            alertMsg.innerText = result.message || 'Đăng nhập thành công!';
            
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 800);
        } else {
            alertBox.className = 'alert alert-danger border-0 shadow-sm mb-4 py-3 d-block animate-shake';
            alertIcon.className = 'bi bi-exclamation-triangle-fill fs-4';
            alertMsg.innerText = result.message;
            
            btn.disabled = false;
            btnText.innerText = 'XÁC NHẬN ĐĂNG NHẬP';
            btnLoader.classList.add('d-none');
        }
    } catch (error) {
        console.error('Lỗi:', error);
        alertBox.className = 'alert alert-danger border-0 shadow-sm mb-4 py-3 d-block';
        alertMsg.innerText = 'Lỗi kết nối máy chủ!';
        btn.disabled = false;
        btnText.innerText = 'XÁC NHẬN ĐĂNG NHẬP';
        btnLoader.classList.add('d-none');
    }
});
</script>

<style>
    .fw-black { font-weight: 900; }
    
    .forgot-link {
        color: #2563eb !important; 
        font-weight: 800 !important;
        font-size: 0.8rem;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: 0.3s;
    }
    .forgot-link:hover {
        color: #1d4ed8 !important;
        border-bottom-color: #2563eb;
    }
    
    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-6px); } 75% { transform: translateX(6px); } }
    
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3) !important; }
</style>

@include('user.layouts.footer')