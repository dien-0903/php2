<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập quản trị - MD</title>
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

        .login-card { 
            width: 100%; 
            max-width: 420px; 
            border: none; 
            background: #ffffff; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); 
            border-radius: 16px; 
            overflow: hidden; 
        }

        /* Header đơn giản, icon tròn */
        .login-header { 
            padding: 40px 30px 10px; 
            text-align: center; 
        }
        
        .icon-circle {
            width: 70px; height: 70px;
            background: #e0e7ff; color: #4f46e5;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: 2rem;
        }

        /* Input sạch sẽ */
        .form-control { 
            background-color: #f8fafc; 
            border: 1px solid #e2e8f0; 
            color: #334155; 
            border-radius: 8px; 
            padding: 12px 14px; 
            font-size: 0.95rem;
        }
        
        .form-control:focus { 
            background-color: #fff; 
            border-color: #4f46e5; 
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); 
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            color: #64748b;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }
        
        /* Fix border input khi dùng group */
        .input-group .form-control { border-left: none; }

        .btn-admin { 
            background-color: #4f46e5; 
            border: none; border-radius: 8px; 
            padding: 12px; font-weight: 600; color: #fff; 
            transition: all 0.3s;
        }
        
        .btn-admin:hover { 
            background-color: #4338ca; 
            transform: translateY(-1px); 
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .text-label {
            color: #64748b; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; display: block;
        }

        .link-forgot {
            color: #4f46e5; font-size: 0.85rem; text-decoration: none; font-weight: 500;
        }
        .link-forgot:hover { text-decoration: underline; }

        .animate-shake { animation: shake 0.4s ease-in-out; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="icon-circle">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h4 class="fw-bold text-dark mb-1">Quản trị hệ thống</h4>
        <p class="text-muted small">Vui lòng đăng nhập để tiếp tục</p>
    </div>

    <div class="card-body p-4 pt-2">
        <div id="auth-alert" class="alert d-none border-0 small mb-4 py-2 text-center rounded-3"></div>

        <form id="adminLoginForm">
            <div class="mb-3">
                <label class="text-label">EMAIL</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control shadow-none" placeholder="admin@MD.vn" required>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="text-label mb-0">MẬT KHẨU</label>
                    <a href="{{ BASE_URL }}/adminauth/forgot" class="link-forgot">Quên mật khẩu?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control shadow-none" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" id="submitBtn" class="btn btn-admin w-100 mb-3">
                ĐĂNG NHẬP
            </button>
            
            <div class="text-center mt-4 border-top pt-3">
                <a href="{{ BASE_URL }}/" class="text-secondary text-decoration-none small fw-bold">
                    <i class="bi bi-shop me-1"></i> Quay về trang chủ
                </a>
            </div>
        </form>
    </div>
</div>

<script>
/* Script giữ nguyên logic của bạn */
document.getElementById('adminLoginForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('auth-alert');
    const formData = new FormData(this);
    
    btn.disabled = true; btn.innerText = 'ĐANG XỬ LÝ...'; alertBox.classList.add('d-none');

    try {
        const response = await fetch('{{ BASE_URL }}/adminauth/postLogin', { method: 'POST', body: formData });
        const result = await response.json();

        if (result.success) {
            alertBox.className = 'alert alert-success d-block bg-success-subtle text-success border-0';
            alertBox.innerText = result.message;
            setTimeout(() => window.location.href = result.redirect, 800);
        } else {
            alertBox.className = 'alert alert-danger d-block bg-danger-subtle text-danger border-0 animate-shake';
            alertBox.innerText = result.message;
            btn.disabled = false; btn.innerText = 'ĐĂNG NHẬP';
        }
    } catch (error) {
        alertBox.className = 'alert alert-danger d-block bg-danger-subtle text-danger border-0';
        alertBox.innerText = 'Lỗi kết nối hệ thống!';
        btn.disabled = false; btn.innerText = 'ĐĂNG NHẬP';
    }
});
</script>

</body>
</html>