@include('admin.layouts.header')

<div class="container mt-5 text-dark pb-5">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center animate-slide-down py-3">
            <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                <i class="bi bi-check2-all text-success fs-5"></i>
            </div>
            <div class="fw-semibold text-success">{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif

    @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center animate-shake py-3">
            <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3">
                <i class="bi bi-exclamation-triangle text-danger fs-5"></i>
            </div>
            <div class="fw-semibold text-danger">{{ $_SESSION['error'] }}</div>
            @php unset($_SESSION['error']) @endphp
        </div>
    @endif

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
        <div>
            <h2 class="fw-black text-slate-900 uppercase tracking-tighter mb-1">
                <i class="bi bi-arrows-expand text-primary me-2"></i>Quản lý Kích thước
            </h2>
            <p class="text-slate-500 small mb-0 font-medium">Thiết lập các thông số về dung lượng, kích cỡ cho sản phẩm</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-blue transition-all hover-scale" 
                data-bs-toggle="modal" data-bs-target="#addSizeModal">
            <i class="bi bi-plus-lg me-2"></i>THÊM MỚI
        </button>
    </div>

    <div class="card border-0 shadow-soft rounded-5 overflow-hidden bg-white border border-slate-100">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-dark border-0">
                <thead>
                    <tr class="bg-slate-900">
                        <th class="ps-5 py-4 border-0 fw-bold uppercase tracking-widest text-black" width="120">ID</th>
                        <th class="py-4 border-0 fw-bold uppercase tracking-widest text-black">Tên Kích thước / Thông số</th>
                        <th class="text-end pe-5 py-4 border-0 fw-bold uppercase tracking-widest text-black">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sizes as $s)
                    <tr class="transition-all hover:bg-slate-50">
                        <td class="ps-5 py-4 text-muted font-monospace small">
                            #{{ $s['id'] }}
                        </td>
                        <td class="py-4">
                            <span class="fw-bold text-slate-800 fs-6">{{ $s['name'] }}</span>
                        </td>
                        <td class="text-end pe-5 py-4">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn-action btn-edit bg-warning bg-opacity-10 text-warning btn-edit-size" 
                                        data-bs-toggle="modal" data-bs-target="#editSizeModal"
                                        data-id="{{ $s['id'] }}" data-name="{{ htmlspecialchars($s['name']) }}"
                                        title="Chỉnh sửa">
                                    <i class="bi-pencil-square text-warning"></i>
                                </button>
                                <a href="{{ BASE_URL }}/adminsize/destroy/{{ $s['id'] }}" 
                                   class="btn-action btn-delete bg-danger bg-opacity-10 text-danger" 
                                   onclick="return confirm('Xác nhận xóa kích thước này khỏi hệ thống?')">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center p-5 text-muted italic border-0">
                            <div class="py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                <span>Chưa có dữ liệu kích thước nào được tạo.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if (isset($totalPages) && $totalPages > 1)
    <nav class="mt-5">
        <ul class="pagination justify-content-center gap-2">
            @for ($i = 1; $i <= $totalPages; $i++)
                <li class="page-item">
                    <a class="page-link border-0 shadow-sm rounded-3 px-3 py-2 fw-bold transition-all {{ ($currentPage == $i) ? 'bg-primary text-white scale-110 shadow-primary' : 'bg-white text-slate-600 hover:bg-light' }}" 
                       href="{{ BASE_URL }}/adminsize/index?page={{ $i }}&search={{ $search ?? '' }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor
        </ul>
    </nav>
    @endif
</div>

@include('admin.size.them')
@include('admin.size.edit')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.btn-edit-size');
    const editForm = document.getElementById('editSizeForm');
    
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            editForm.action = '{{ rtrim(BASE_URL, "/") }}/adminsize/update/' + id;
            document.getElementById('edit_size_name').value = name;
        });
    });
});
</script>

<style>
    .fw-black { font-weight: 900; }
    .shadow-soft { box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .shadow-blue { box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    
    .btn-action {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        border: none;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    .btn-edit:hover { background-color: #ffc107 !important; color: #fff !important; }
    .btn-delete:hover { background-color: #dc3545 !important; color: #fff !important; }

    .hover-scale:hover { transform: scale(1.05); }

    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-15px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    .animate-shake { animation: shake 0.3s ease-in-out; }

    .bg-slate-900 { background-color: #0f172a !important; }
    .table thead th { font-size: 0.9rem; letter-spacing: 0.05em; }
    .table tbody tr td { border-bottom-color: #f1f5f9; }
</style>

@include('admin.layouts.footer')