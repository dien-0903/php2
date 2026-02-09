<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục quyền truy cập - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #f3f4f6; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-family: 'Segoe UI', system-ui, sans-serif; 
        }

        .auth-card { 
            width: 100%; 
            max-width: 420px; 
            border-radius: 16px; 
            border: none; 
            background: #ffffff; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); 
            overflow: hidden; 
        }

        .auth-header { 
            background: #fff; 
            padding: 40px 30px 10px 30px; 
            text-align: center; 
            color: #1e293b; 
        }
        
        .icon-circle {
            width: 70px;
            height: 70px;
            background: #e0e7ff;
            color: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }

        .form-control { 
            background-color: #f8fafc; 
            border: 1px solid #e2e8f0; 
            color: #334155; 
            border-radius: 8px; 
            padding: 14px; 
            font-size: 0.95rem;
        }
        
        .form-control:focus { 
            background-color: #fff; 
            border-color: #4f46e5; 
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); 
            color: #0f172a;
        }

        .btn-reset { 
            background-color: #4f46e5; 
            border: none; 
            border-radius: 8px; 
            padding: 14px; 
            font-weight: 600; 
            color: #fff; 
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .btn-reset:hover { 
            background-color: #4338ca; 
            transform: translateY(-1px); 
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .text-label {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
        }

        .animate-shake { animation: shake 0.4s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="auth-header">
        <div class="icon-circle">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h4 class="fw-bold mb-1">Khôi phục mật khẩu</h4>
        <p class="text-muted small">Nhập email quản trị để thiết lập lại mật khẩu</p>
    </div>

    <div class="card-body p-4 pt-2">
        <div id="auth-alert" class="alert d-none border-0 small mb-4 py-2 text-center rounded-3"></div>

        <form id="forgotAdminForm">
            <div class="mb-3">
                <label class="text-label">EMAIL HỆ THỐNG</label>
                <input type="email" name="email" class="form-control" placeholder="admin@domain.com" required>
            </div>

            <div class="mb-3">
                <label class="text-label">MẬT KHẨU MỚI</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="mb-4">
                <label class="text-label">XÁC NHẬN MẬT KHẨU</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-reset w-100 mb-3">
                XÁC NHẬN ĐỔI
            </button>

            <div class="text-center mt-4">
                <a href="{{ BASE_URL }}/adminauth/login" class="text-decoration-none small fw-bold text-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Quay về đăng nhập
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
    btn.disabled = true; btn.innerText = 'ĐANG XỬ LÝ...'; alertBox.classList.add('d-none');
    try {
        const response = await fetch('{{ BASE_URL }}/adminauth/postForgot', { method: 'POST', body: formData });
        const result = await response.json();
        if (result.success) {
            alertBox.className = 'alert alert-success d-block bg-success-subtle text-success';
            alertBox.innerText = result.message;
            setTimeout(() => window.location.href = result.redirect, 1500);
        } else {
            alertBox.className = 'alert alert-danger d-block bg-danger-subtle text-danger animate-shake';
            alertBox.innerText = result.message;
            btn.disabled = false; btn.innerText = 'XÁC NHẬN ĐỔI';
        }
    } catch (error) {
        alertBox.className = 'alert alert-danger d-block bg-danger-subtle text-danger';
        alertBox.innerText = 'Lỗi kết nối máy chủ!';
        btn.disabled = false; btn.innerText = 'XÁC NHẬN ĐỔI';
    }
});
</script>
</body>
</html>