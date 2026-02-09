@include('user.layouts.header')

<div class="bg-light min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(isset($_SESSION['success']))
                    <div class="alert alert-success border-0 shadow-sm rounded-4 py-3 mb-4 animate-slide-down">
                        <div class="d-flex align-items-center fw-bold text-success">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <span>{{ $_SESSION['success'] }}</span>
                        </div>
                        @php unset($_SESSION['success']) @endphp
                    </div>
                @endif

                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <h1 class="h3 mb-2 text-center fw-bold">Liên Hệ Với Chúng Tôi</h1>
                        <p class="text-muted text-center mb-5">Bạn có thắc mắc? Chúng tôi luôn sẵn sàng lắng nghe. Hãy để lại lời nhắn bên dưới.</p>
                        
                        <form action="{{ rtrim(BASE_URL, '/') }}/contact/store" method="POST">
                            @php 
                                $old = $_SESSION['old'] ?? [];
                                $errors = $_SESSION['errors'] ?? [];
                                unset($_SESSION['old'], $_SESSION['errors']);
                            @endphp

                            <div class="row g-4 text-start">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="full_name" 
                                           class="form-control rounded-3 py-2.5 border-slate-200 shadow-none {{ isset($errors['full_name']) ? 'is-invalid' : '' }}" 
                                           placeholder="Nguyễn Văn A" 
                                           value="{{ $old['full_name'] ?? '' }}">
                                    @if(isset($errors['full_name'])) <div class="invalid-feedback">{{ $errors['full_name'] }}</div> @endif
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Địa chỉ Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" 
                                           class="form-control rounded-3 py-2.5 border-slate-200 shadow-none {{ isset($errors['email']) ? 'is-invalid' : '' }}" 
                                           placeholder="example@gmail.com" 
                                           value="{{ $old['email'] ?? '' }}">
                                    @if(isset($errors['email'])) <div class="invalid-feedback">{{ $errors['email'] }}</div> @endif
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" 
                                           class="form-control rounded-3 py-2.5 border-slate-200 shadow-none {{ isset($errors['phone']) ? 'is-invalid' : '' }}" 
                                           placeholder="09xxxxxxxx" 
                                           value="{{ $old['phone'] ?? '' }}">
                                    @if(isset($errors['phone'])) <div class="invalid-feedback">{{ $errors['phone'] }}</div> @endif
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Chủ đề <span class="text-danger">*</span></label>
                                    <input type="text" name="subject" 
                                           class="form-control rounded-3 py-2.5 border-slate-200 shadow-none {{ isset($errors['subject']) ? 'is-invalid' : '' }}" 
                                           placeholder="Hỗ trợ đơn hàng, góp ý..." 
                                           value="{{ $old['subject'] ?? '' }}">
                                    @if(isset($errors['subject'])) <div class="invalid-feedback">{{ $errors['subject'] }}</div> @endif
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-secondary text-uppercase tracking-wider">Lời nhắn của bạn <span class="text-danger">*</span></label>
                                    <textarea name="message" class="form-control rounded-4 border-slate-200 shadow-none {{ isset($errors['message']) ? 'is-invalid' : '' }}" 
                                              rows="5" placeholder="Nhập nội dung chi tiết tại đây...">{{ $old['message'] ?? '' }}</textarea>
                                    @if(isset($errors['message'])) <div class="invalid-feedback">{{ $errors['message'] }}</div> @endif
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg transition-all hover-lift">
                                        GỬI THÔNG TIN LIÊN HỆ <i class="bi bi-send-fill ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="row g-4 mt-2">
                    <div class="col-md-4">
                        <div class="card text-center h-100 shadow-sm border-0 rounded-4 transition-all hover-lift">
                            <div class="card-body py-4">
                                <div class="mb-3 text-primary"><i class="bi bi-envelope-paper-fill fs-2"></i></div>
                                <h6 class="fw-bold">Email</h6>
                                <p class="small text-muted mb-0">contact@simpleshop.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center h-100 shadow-sm border-0 rounded-4 transition-all hover-lift">
                            <div class="card-body py-4">
                                <div class="mb-3 text-primary"><i class="bi bi-telephone-inbound-fill fs-2"></i></div>
                                <h6 class="fw-bold">Điện thoại</h6>
                                <p class="small text-muted mb-0">+84 123 456 789</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center h-100 shadow-sm border-0 rounded-4 transition-all hover-lift">
                            <div class="card-body py-4">
                                <div class="mb-3 text-primary"><i class="bi bi-geo-alt-fill fs-2"></i></div>
                                <h6 class="fw-bold">Địa chỉ</h6>
                                <p class="small text-muted mb-0">123 Đường Số 1, Quận 1, HCM</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .font-inter { font-family: 'Inter', sans-serif; }
    .hover-lift:hover { transform: translateY(-5px); transition: 0.3s; }
    .border-slate-200 { border-color: #e2e8f0; }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-15px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    input::placeholder, textarea::placeholder { font-size: 0.9rem; opacity: 0.5; }
    .invalid-feedback { font-weight: 600; font-size: 0.75rem; margin-left: 5px; }
</style>

@include('user.layouts.footer')