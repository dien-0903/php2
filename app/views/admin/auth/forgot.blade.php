<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục quyền truy cập - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #0f172a; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; margin: 0; }
        .auth-card { width: 100%; max-width: 400px; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.1); background: #1e293b; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
        .auth-header { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 40px 20px; text-align: center; color: #fff; }
        .auth-header i { font-size: 3rem; margin-bottom: 10px; display: block; }
        .form-control { background-color: #334155; border: 1px solid #475569; color: #fff; border-radius: 12px; padding: 12px; transition: 0.3s; }
        .form-control:focus { background-color: #334155; border-color: #f59e0b; color: #fff; box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.15); }
        .btn-reset { background-color: #f59e0b; border: none; border-radius: 12px; padding: 12px; font-weight: 700; color: #fff; transition: 0.3s; text-transform: uppercase; }
        .btn-reset:hover { background-color: #d97706; transform: translateY(-2px); }
        .btn-reset:disabled { opacity: 0.7; transform: none; }
        .text-muted { color: #94a3b8 !important; }
        .animate-shake { animation: shake 0.4s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    </style>
</head>
<body>

<div class="auth-card shadow-lg">
    <div class="auth-header">
        <i class="bi bi-key-fill"></i>
        <h3 class="fw-bold mb-0">KHÔI PHỤC ADMIN</h3>
        <p class="small opacity-75 mt-1">Cập nhật mật khẩu truy cập hệ thống</p>
    </div>

    <div class="card-body p-4 p-md-5">
        <div id="auth-alert" class="alert d-none border-0 small mb-4 py-2 text-center rounded-3"></div>

        <form id="forgotAdminForm">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Email quản trị viên</label>
                <input type="email" name="email" class="form-control" placeholder="admin@techmart.vn" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Xác nhận mật khẩu</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-reset w-100 shadow-sm mb-3">
                CẬP NHẬT MẬT KHẨU
            </button>

            <div class="text-center">
                <a href="{{ BASE_URL }}/adminauth/login" class="text-decoration-none small fw-bold" style="color: #f59e0b;">
                    <i class="bi bi-arrow-left me-1"></i> Quay lại Đăng nhập
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('forgotAdminForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('auth-alert');
    const formData = new FormData(this);
    
    btn.disabled = true;
    btn.innerText = 'ĐANG XỬ LÝ...';
    alertBox.classList.add('d-none');

    try {
        const response = await fetch('{{ BASE_URL }}/adminauth/postForgot', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success d-block border-0 bg-success bg-opacity-10 text-success';
            alertBox.innerText = result.message;
            setTimeout(() => window.location.href = result.redirect, 1500);
        } else {
            alertBox.className = 'alert alert-danger d-block border-0 bg-danger bg-opacity-10 text-danger animate-shake';
            alertBox.innerText = result.message;
            btn.disabled = false;
            btn.innerText = 'CẬP NHẬT MẬT KHẨU';
        }
    } catch (error) {
        alertBox.className = 'alert alert-danger d-block border-0 bg-danger bg-opacity-10 text-danger';
        alertBox.innerText = 'Lỗi kết nối máy chủ!';
        btn.disabled = false;
        btn.innerText = 'CẬP NHẬT MẬT KHẨU';
    }
});
</script>

</body>
</html>