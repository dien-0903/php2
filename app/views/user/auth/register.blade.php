@include('user.layouts.header')

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 75vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-5 overflow-hidden">
                <div class="bg-primary p-5 text-center text-white">
                    <i class="bi bi-person-plus-fill display-1"></i>
                    <h2 class="fw-black mt-3 uppercase tracking-tighter">TẠO TÀI KHOẢN</h2>
                    <p class="text-white-50 small mb-0">Trở thành thành viên TechMart ngay hôm nay</p>
                </div>
                
                <div class="card-body p-5 bg-white text-dark">
                    <div id="auth-alert" class="alert d-none border-0 shadow-sm mb-4 py-3 animate-slide-in">
                        <div class="d-flex align-items-center">
                            <i id="alert-icon" class="bi me-3 fs-4"></i>
                            <span id="alert-message" class="fw-bold small"></span>
                        </div>
                    </div>

                    <form id="registerForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small uppercase tracking-wider">Họ và Tên</label>
                            <div class="input-group bg-light rounded-3 overflow-hidden border">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="fullname" class="form-control bg-transparent border-0 py-3 shadow-none" 
                                       placeholder="Nguyễn Văn A" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary small uppercase tracking-wider">Địa chỉ Email</label>
                            <div class="input-group bg-light rounded-3 overflow-hidden border">
                                <span class="input-group-text bg-transparent border-0"><i class="bi bi-envelope-at text-muted"></i></span>
                                <input type="email" name="email" class="form-control bg-transparent border-0 py-3 shadow-none" 
                                       placeholder="name@example.com" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small uppercase tracking-wider">Mật khẩu</label>
                                <input type="password" name="password" class="form-control bg-light border-0 py-3 rounded-3 shadow-none border" 
                                       placeholder="••••••••" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-secondary small uppercase tracking-wider">Xác nhận</label>
                                <input type="password" name="confirm_password" class="form-control bg-light border-0 py-3 rounded-3 shadow-none border" 
                                       placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-lg mb-4 border-0 transition-all">
                            <span id="btnText">ĐĂNG KÝ NGAY</span>
                            <div id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status"></div>
                        </button>

                        <div class="text-center">
                            <p class="text-muted small mb-0">
                                Đã có tài khoản? <a href="{{ BASE_URL }}/auth/login" class="text-primary fw-bold text-decoration-none">Đăng nhập</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault(); 
    
    const form = this;
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const alertBox = document.getElementById('auth-alert');
    const alertMsg = document.getElementById('alert-message');
    const alertIcon = document.getElementById('alert-icon');
    
    btn.disabled = true;
    btnText.innerText = 'ĐANG TẠO TÀI KHOẢN...';
    btnLoader.classList.remove('d-none');
    alertBox.classList.add('d-none');

    try {
        const formData = new FormData(form);
        const response = await fetch('{{ rtrim(BASE_URL, "/") }}/auth/postRegister', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success border-0 shadow-sm mb-4 py-3 d-block';
            alertIcon.className = 'bi bi-check-circle-fill fs-4';
            alertMsg.innerText = result.message;
            
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1200);
        } else {
            alertBox.className = 'alert alert-danger border-0 shadow-sm mb-4 py-3 d-block animate-shake';
            alertIcon.className = 'bi bi-exclamation-triangle-fill fs-4';
            alertMsg.innerText = result.message;
            
            btn.disabled = false;
            btnText.innerText = 'ĐĂNG KÝ NGAY';
            btnLoader.classList.add('d-none');
        }
    } catch (error) {
        console.error('Lỗi:', error);
        alert('Có lỗi kết nối hệ thống, vui lòng thử lại sau!');
        btn.disabled = false;
        btnText.innerText = 'ĐĂNG KÝ NGAY';
        btnLoader.classList.add('d-none');
    }
});
</script>

<style>
    .fw-black { font-weight: 900; }
    .animate-shake { animation: shake 0.4s ease-in-out; }
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-6px); } 75% { transform: translateX(6px); } }
    .animate-slide-in { animation: slideIn 0.4s ease-out; }
    @keyframes slideIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3) !important; }
</style>

@include('user.layouts.footer')