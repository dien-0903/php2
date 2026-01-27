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
                <i class="bi bi-palette-fill text-primary me-2"></i>Bảng màu hệ thống
            </h2>
            <p class="text-slate-500 small mb-0 font-medium">Quản lý các thuộc tính màu sắc hiển thị trên trang sản phẩm</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-blue transition-all hover-scale" 
                data-bs-toggle="modal" data-bs-target="#addColorModal">
            <i class="bi bi-plus-lg me-2"></i>THÊM MÀU MỚI
        </button>
    </div>

    <div class="card border-0 shadow-soft rounded-5 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-dark border-0">
                <thead>
                    <tr class="bg-slate-900">
                        <th class="ps-5 py-4 border-0 fw-bold uppercase tracking-widest text-black" width="140">Mẫu màu</th>
                        <th class="py-4 border-0 fw-bold uppercase tracking-widest text-black">Tên hiển thị</th>
                        <th class="py-4 border-0 fw-bold uppercase tracking-widest text-black">Mã định danh (Hex)</th>
                        <th class="text-end pe-5 py-4 border-0 fw-bold uppercase tracking-widest text-black">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($colors as $c)
                    <tr class="transition-all hover:bg-slate-50">
                        <td class="ps-5 py-3">
                            <div class="position-relative d-inline-block">
                                <div class="color-preview-circle shadow-sm" 
                                     style="background-color: {{ $c['hex_code'] ?: '#cccccc' }};">
                                </div>
                                @if(in_array(strtolower($c['hex_code'] ?? ''), ['#ffffff', '#fff', '#f8fafc', '#f1f5f9']))
                                    <div class="position-absolute top-0 start-0 w-100 h-100 rounded-circle border border-slate-200"></div>
                                @endif
                            </div>
                        </td>
                        <td class="py-3 text-nowrap">
                            <span class="fw-bold text-slate-800 fs-6">{{ $c['name'] }}</span>
                        </td>
                        <td class="py-3 text-nowrap">
                            <div class="d-flex align-items-center">
                                <code class="bg-slate-100 text-primary px-3 py-1.5 rounded-3 fw-bold small border border-slate-200 font-monospace">
                                    {{ strtoupper($c['hex_code'] ?: 'N/A') }}
                                </code>
                            </div>
                        </td>
                        <td class="text-end pe-5 py-3">
                            <div class="d-flex justify-content-end gap-2">
                                <button class="btn-action btn-edit bg-warning bg-opacity-10 text-warning btn-edit-color" 
                                        data-bs-toggle="modal" data-bs-target="#editColorModal"
                                        data-id="{{ $c['id'] }}" 
                                        data-name="{{ htmlspecialchars($c['name']) }}" 
                                        data-hex="{{ $c['hex_code'] }}"
                                        title="Chỉnh sửa">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>
                                <a href="{{ BASE_URL }}/admincolor/destroy/{{ $c['id'] }}" 
                                   class="btn-action btn-delete bg-danger bg-opacity-10 text-danger" 
                                   onclick="return confirm('Bạn có chắc muốn xóa màu này? Thao tác này sẽ cập nhật các sản phẩm liên quan.')"
                                   title="Xóa bỏ">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center p-5">
                            <div class="py-5">
                                <i class="bi bi-palette2 text-slate-200 display-1 d-block mb-3"></i>
                                <h5 class="text-slate-400 fw-bold">Chưa có dữ liệu màu sắc nào</h5>
                                <p class="text-slate-400 small">Bắt đầu bằng cách thêm một màu mới cho hệ thống.</p>
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
                       href="{{ BASE_URL }}/admincolor/index?page={{ $i }}&search={{ $search ?? '' }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor
        </ul>
    </nav>
    @endif
</div>
@include('admin.color.them')
@include('admin.color.edit')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-color');
    const editForm = document.getElementById('editColorForm');
    
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const hex = this.dataset.hex || '#000000';

            editForm.action = '{{ rtrim(BASE_URL, "/") }}/admincolor/update/' + id;
            document.getElementById('edit_color_name').value = name;
            document.getElementById('edit_color_hex').value = hex.toUpperCase();
            document.getElementById('edit_picker').value = hex;
        });
    });
});
</script>

<style>
    .fw-black { font-weight: 900; }
    .shadow-soft { box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05); }
    .shadow-blue { box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.2); }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); }
    
    .color-preview-circle {
        width: 42px;
        height: 42px;
        border-radius: 50%; 
        border: 3px solid #fff;
        box-shadow: 0 0 0 1px #e2e8f0; 
        transition: transform 0.2s ease-in-out;
    }
    tr:hover .color-preview-circle {
        transform: scale(1.15);
    }

    .btn-action {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        transition: 0.2s;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .btn-edit:hover { background-color: #ffc107 !important; color: #fff !important; }
    .btn-delete:hover { background-color: #dc3545 !important; color: #fff !important; }

    .hover-scale:hover { transform: scale(1.03); }

    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    
    @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
    .animate-shake { animation: shake 0.3s ease-in-out; }
    
    .table thead th { font-size: 0.9rem; letter-spacing: 0.08em; } 
    .table tbody tr td { border-bottom-color: #f8fafc; padding-top: 12px; padding-bottom: 12px; }
    .text-nowrap { white-space: nowrap; }

    .bg-slate-900 { background-color: #0f172a !important; }
</style>

@include('admin.layouts.footer')