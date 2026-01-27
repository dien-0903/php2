@include('admin.layouts.header')

<div class="container mt-4 text-dark">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center rounded-4 animate-slide-down">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold uppercase"><i class="bi bi-ticket-perforated text-primary me-2"></i>Quản lý Mã giảm giá</h2>
        <button class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addCouponModal">
            <i class="bi bi-plus-lg me-1"></i>Tạo mã mới
        </button>
    </div>

    <div class="card p-3 mb-4 shadow-sm border-0 rounded-4 bg-white border border-slate-100">
        <form action="{{ rtrim(BASE_URL, '/') }}/admincoupon/index" method="GET" class="row g-2">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted rounded-start-pill ps-3">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 shadow-none rounded-end-pill py-2"
                        placeholder="Tìm theo mã code..." value="{{ $search ?? '' }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark w-100 fw-bold rounded-pill">TÌM KIẾM</button>
            </div>
            @if(!empty($search))
                <div class="col-md-2">
                    <a href="{{ rtrim(BASE_URL, '/') }}/admincoupon/index" class="btn btn-outline-secondary w-100 rounded-pill">XÓA LỌC</a>
                </div>
            @endif
        </form>
    </div>

    <div class="row g-4">
        @forelse ($coupons as $c)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden border-2 border-dashed border-slate-200 hover:border-primary transition-all bg-white group">
                <div class="card-body p-4 text-dark d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge {{ $c['type'] == 'percent' ? 'bg-primary' : 'bg-success' }} rounded-pill px-3 fw-bold">
                            {{ $c['type'] == 'percent' ? 'Giảm %' : 'Giảm tiền' }}
                        </span>
                        <span class="badge {{ $c['status'] ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary' }} border-0 px-3">
                            {{ $c['status'] ? 'Hoạt động' : 'Tạm dừng' }}
                        </span>
                    </div>

                    <h3 class="fw-black text-dark font-mono fs-2 tracking-tighter mb-1 uppercase">{{ $c['code'] }}</h3>
                    <p class="text-muted small mb-4">
                        Giá trị: <span class="fw-bold text-danger fs-5">
                            {{ $c['type'] == 'percent' ? $c['value'].'%' : number_format($c['value']).'đ' }}
                        </span>
                    </p>
                    
                    <div class="d-flex gap-2 border-top pt-3 mt-auto">
                        <button class="btn btn-sm btn-outline-warning flex-grow-1 rounded-pill fw-bold btn-edit-coupon" 
                                data-bs-toggle="modal" data-bs-target="#editCouponModal"
                                data-id="{{ $c['id'] }}"
                                data-code="{{ $c['code'] }}"
                                data-type="{{ $c['type'] }}"
                                data-value="{{ $c['value'] }}"
                                data-status="{{ $c['status'] }}">
                            <i class="bi bi-pencil-square me-1"></i>Sửa
                        </button>
                        <a href="{{ rtrim(BASE_URL, '/') }}/admincoupon/destroy/{{ $c['id'] }}" 
                           class="btn btn-sm btn-outline-danger px-3 rounded-pill"
                           onclick="return confirm('Xác nhận xóa mã ưu đãi {{ $c['code'] }}?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 bg-white rounded-4 border border-dashed border-slate-200">
                <i class="bi bi-ticket-detailed fs-1 text-slate-200"></i>
                <p class="text-muted mt-3 mb-0">Không tìm thấy mã giảm giá nào phù hợp.</p>
            </div>
        </div>
        @endforelse
    </div>

    @if (isset($totalPages) && $totalPages > 1)
    <nav class="mt-5 mb-5">
        <ul class="pagination justify-content-center gap-2">
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ ($currentPage == $i) ? 'active' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 fw-bold {{ ($currentPage == $i) ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark hover:bg-light' }}" 
                       href="{{ rtrim(BASE_URL, '/') }}/admincoupon/index?page={{ $i }}&search={{ urlencode($search ?? '') }}">{{ $i }}</a>
                </li>
            @endfor
        </ul>
    </nav>
    @endif
</div>

@include('admin.coupon.them')
@include('admin.coupon.edit')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.btn-edit-coupon');
        const editForm = document.getElementById('editCouponForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                if (editForm) {
                    editForm.action = '{{ rtrim(BASE_URL, "/") }}/admincoupon/update/' + id;
                }

                document.getElementById('edit_code').value = this.dataset.code;
                document.getElementById('edit_type').value = this.dataset.type;
                document.getElementById('edit_value').value = this.dataset.value;
                document.getElementById('edit_status').checked = (parseInt(this.dataset.status) === 1);
            });
        });

        @if(isset($_SESSION['error_type']))
            const modalId = "{{ $_SESSION['error_type'] === 'add' ? '#addCouponModal' : '#editCouponModal' }}";
            const targetModal = document.querySelector(modalId);
            if(targetModal) {
                const bootstrapModal = new bootstrap.Modal(targetModal);
                bootstrapModal.show();
            }
        @endif
    });
</script>

<style>
    .fw-black { font-weight: 900; }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

@include('admin.layouts.footer')