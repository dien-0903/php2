<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TechMart System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #0f172a;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            background: #1e293b;
        }
        .login-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 45px 20px;
            text-align: center;
            color: #fff;
        }
        .login-header i { font-size: 3.5rem; margin-bottom: 10px; display: block; }
        .form-control {
            background-color: #334155;
            border: 1px solid #475569;
            color: #f8fafc;
            border-radius: 12px;
            padding: 12px 15px;
            transition: 0.3s;
        }
        .form-control:focus {
            background-color: #334155;
            color: #fff;
            box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.2);
            border-color: #10b981;
        }
        .form-control::placeholder { color: #94a3b8; }
        .input-group-text {
            background-color: #334155;
            border: 1px solid #475569;
            color: #94a3b8;
        }
        .btn-admin {
            background-color: #10b981;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            color: #fff;
        }
        .btn-admin:hover {
            background-color: #059669;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }
        .btn-admin:disabled { opacity: 0.7; transform: none; }
        .text-muted { color: #94a3b8 !important; }
        .animate-shake { animation: shake 0.4s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
        .animate-slide-in { animation: slideIn 0.4s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="bi bi-shield-lock-fill"></i>
        <h3 class="fw-bold mb-0" style="letter-spacing: -1px;">HỆ THỐNG QUẢN TRỊ</h3>
        <p class="small opacity-75 mt-1">TechMart Internal Control</p>
    </div>

    <div class="card-body p-4 p-md-5">
        <div id="auth-alert" class="alert d-none border-0 small mb-4 py-2 text-center rounded-3 animate-slide-in"></div>

        <form id="adminLoginForm">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Email Admin</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control shadow-none" placeholder="admin@techmart.vn" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between">
                    <label class="form-label small fw-bold text-muted text-uppercase">Mật khẩu</label>
                    <a href="{{ BASE_URL }}/adminauth/forgot" class="small text-decoration-none" style="color: #10b981;">Quên mật khẩu?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control shadow-none" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-admin w-100 shadow-sm mb-3">
                XÁC THỰC QUYỀN TRUY CẬP
            </button>
        </form>
    </div>

    <div class="card-footer border-0 pb-4 text-center" style="background: transparent;">
        <a href="{{ BASE_URL }}/" class="text-muted small text-decoration-none hover:text-white transition">
            <i class="bi bi-house-door me-1"></i> Quay lại cửa hàng
        </a>
    </div>
</div>

<script>
document.getElementById('adminLoginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('auth-alert');
    const formData = new FormData(this);
    
    btn.disabled = true;
    btn.innerText = 'ĐANG XÁC THỰC...';
    alertBox.classList.add('d-none');

    try {
        const response = await fetch('{{ BASE_URL }}/adminauth/postLogin', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success d-block border-0 bg-success bg-opacity-10 text-success';
            alertBox.innerText = result.message;
            setTimeout(() => window.location.href = result.redirect, 800);
        } else {
            alertBox.className = 'alert alert-danger d-block border-0 bg-danger bg-opacity-10 text-danger animate-shake';
            alertBox.innerText = result.message;
            
            btn.disabled = false;
            btn.innerText = 'XÁC THỰC QUYỀN TRUY CẬP';
        }
    } catch (error) {
        alertBox.className = 'alert alert-danger d-block border-0 bg-danger bg-opacity-10 text-danger';
        alertBox.innerText = 'Lỗi kết nối hệ thống!';
        btn.disabled = false;
        btn.innerText = 'XÁC THỰC QUYỀN TRUY CẬP';
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>