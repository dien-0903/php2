@include('admin.layouts.header')

<div class="container mt-4 text-dark mb-5">
    <!-- Hệ thống thông báo phản hồi -->
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center rounded-4 animate-slide-down">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center rounded-4 animate-slide-down">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['error'] }}</div>
            @php unset($_SESSION['error']) @endphp
        </div>
    @endif

    <!-- Tiêu đề và Điều hướng -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold uppercase tracking-tight">
                <i class="bi bi-geo-alt-fill text-primary me-2"></i>Sổ địa chỉ
            </h2>
            <div class="text-muted small mt-1">Khách hàng: <span class="fw-bold text-dark">{{ $user['fullname'] }}</span></div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="bi bi-plus-lg me-1"></i>THÊM ĐỊA CHỈ
            </button>
            <a href="{{ rtrim(BASE_URL, '/') }}/adminuser/index" class="btn btn-outline-secondary shadow-sm px-4 rounded-pill fw-bold">
                <i class="bi bi-arrow-left me-1"></i>QUAY LẠI
            </a>
        </div>
    </div>

    <!-- Bảng danh sách địa chỉ -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white border border-slate-100">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark border-0">
                    <tr>
                        <th class="ps-4 py-3" width="250">Người nhận & SĐT</th>
                        <th>Địa chỉ chi tiết</th>
                        <th class="text-center" width="130">Mặc định</th>
                        <th class="text-center" width="150">Trạng thái</th>
                        <th class="text-end pe-4" width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($addresses as $addr)
                    @php $isActive = ($addr['status'] ?? 1) == 1; @endphp
                    <tr class="{{ $addr['is_default'] ? 'bg-primary-subtle' : '' }} {{ !$isActive ? 'opacity-75 bg-light' : '' }}">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ $addr['recipient_name'] }}</div>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $addr['phone'] }}</div>
                        </td>
                        <td><div class="small text-dark lh-sm">{{ $addr['address'] }}</div></td>
                        <td class="text-center">
                            @if($addr['is_default'])
                                <span class="badge bg-primary rounded-pill px-3 py-2 extra-small fw-bold shadow-sm">MẶC ĐỊNH</span>
                            @else
                                <a href="{{ rtrim(BASE_URL, '/') }}/adminaddress/set_default/{{ $addr['id'] }}/{{ $user['id'] }}" 
                                   class="text-decoration-none text-muted small hover-primary" title="Đặt làm mặc định">
                                    <i class="bi bi-bookmark"></i> Thiết lập
                                </a>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ rtrim(BASE_URL, '/') }}/adminaddress/toggle_status/{{ $addr['id'] }}/{{ $user['id'] }}" 
                               class="text-decoration-none" onclick="return confirm('Bạn muốn thay đổi trạng thái địa chỉ này?')">
                                @if($isActive)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2 extra-small fw-bold">HOẠT ĐỘNG</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 extra-small fw-bold">VÔ HIỆU</span>
                                @endif
                            </a>
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group shadow-sm rounded-3 overflow-hidden bg-white border">
                                <button class="btn btn-sm btn-white px-3 btn-edit-address" 
                                    data-bs-toggle="modal" data-bs-target="#editAddressModal"
                                    data-id="{{ $addr['id'] }}"
                                    data-name="{{ htmlspecialchars($addr['recipient_name']) }}"
                                    data-phone="{{ $addr['phone'] }}"
                                    data-full="{{ htmlspecialchars($addr['address']) }}"
                                    data-default="{{ $addr['is_default'] }}">
                                    <i class="bi bi-pencil-square text-warning"></i>
                                </button>
                                <a href="{{ rtrim(BASE_URL, '/') }}/adminaddress/destroy/{{ $addr['id'] }}/{{ $user['id'] }}" 
                                   class="btn btn-sm btn-white px-3" onclick="return confirm('Xác nhận xóa địa chỉ này?')">
                                    <i class="bi bi-trash3 text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center p-5 text-muted">Chưa có địa chỉ nào được lưu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Nạp các Modal thành phần -->
@include('admin.address.them')
@include('admin.address.edit')

<script>
document.addEventListener('DOMContentLoaded', function() {
    const API_URL = 'https://provinces.open-api.vn/api/';

    // --- LOGIC API ĐỊA GIỚI HÀNH CHÍNH ---
    async function initProvinceLogic(container) {
        if (!container) return null;

        const pSel = container.querySelector('.province-select');
        const dSel = container.querySelector('.district-select');
        const wSel = container.querySelector('.ward-select');
        const street = container.querySelector('.street-input');
        const hidden = container.querySelector('.address-full-hidden');
        const form = container.querySelector('form');

        if (!pSel || !form) return null;

        async function loadP(selectedName = null) {
            try {
                const res = await fetch(API_URL + 'p/');
                const data = await res.json();
                pSel.innerHTML = '<option value="" selected disabled>Tỉnh/Thành</option>';
                data.forEach(p => {
                    const isSelected = (selectedName && p.name.trim().toLowerCase() === selectedName.trim().toLowerCase()) ? 'selected' : '';
                    pSel.innerHTML += `<option value="${p.name}" data-code="${p.code}" ${isSelected}>${p.name}</option>`;
                });
                return data;
            } catch (err) { console.error("Lỗi API Tỉnh:", err); }
        }

        async function loadD(pCode, selectedName = null) {
            if (!pCode) return;
            try {
                dSel.disabled = false;
                dSel.innerHTML = '<option value="" selected disabled>Đang tải...</option>';
                const res = await fetch(`${API_URL}p/${pCode}?depth=2`);
                const data = await res.json();
                dSel.innerHTML = '<option value="" selected disabled>Quận/Huyện</option>';
                data.districts.forEach(d => {
                    const isSelected = (selectedName && d.name.trim().toLowerCase() === selectedName.trim().toLowerCase()) ? 'selected' : '';
                    dSel.innerHTML += `<option value="${d.name}" data-code="${d.code}" ${isSelected}>${d.name}</option>`;
                });
                return data.districts;
            } catch (err) { console.error("Lỗi API Huyện:", err); }
        }

        async function loadW(dCode, selectedName = null) {
            if (!dCode) return;
            try {
                wSel.disabled = false;
                wSel.innerHTML = '<option value="" selected disabled>Đang tải...</option>';
                const res = await fetch(`${API_URL}d/${dCode}?depth=2`);
                const data = await res.json();
                wSel.innerHTML = '<option value="" selected disabled>Phường/Xã</option>';
                data.wards.forEach(w => {
                    const isSelected = (selectedName && w.name.trim().toLowerCase() === selectedName.trim().toLowerCase()) ? 'selected' : '';
                    wSel.innerHTML += `<option value="${w.name}" ${isSelected}>${w.name}</option>`;
                });
            } catch (err) { console.error("Lỗi API Xã:", err); }
        }

        pSel.addEventListener('change', async function() {
            const code = this.options[this.selectedIndex].dataset.code;
            wSel.innerHTML = '<option value="" selected disabled>Phường/Xã</option>';
            wSel.disabled = true;
            await loadD(code);
        });

        dSel.addEventListener('change', async function() {
            const code = this.options[this.selectedIndex].dataset.code;
            await loadW(code);
        });

        form.addEventListener('submit', function() {
            if (pSel.value && dSel.value && wSel.value && street.value) {
                hidden.value = `${street.value.trim()}, ${wSel.value}, ${dSel.value}, ${pSel.value}`;
            }
        });

        return { loadP, loadD, loadW, pSel, dSel };
    }

    // --- KHỞI TẠO MODAL THÊM ---
    const addModal = document.getElementById('addAddressModal');
    if (addModal) {
        initProvinceLogic(addModal).then(api => api && api.loadP());
    }

    // --- KHỞI TẠO MODAL SỬA ---
    const editModal = document.getElementById('editAddressModal');
    const editAPI = editModal ? initProvinceLogic(editModal) : null;

    // Xử lý khi nhấn nút "Sửa"
    document.querySelectorAll('.btn-edit-address').forEach(btn => {
        btn.addEventListener('click', async function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const phone = this.dataset.phone;
            const fullStr = this.dataset.full || "";
            const isDefault = this.dataset.default;

            // Truy cập trực tiếp vào các ID duy nhất trong Modal Sửa
            const editForm = document.getElementById('editAddressForm');
            const nameInp = document.getElementById('address_edit_name');
            const phoneInp = document.getElementById('address_edit_phone');
            const defCheck = document.getElementById('address_edit_default');

            // 1. Gán dữ liệu cơ bản NGAY LẬP TỨC để tránh cảm giác chờ đợi
            if (editForm) editForm.action = `{{ rtrim(BASE_URL, '/') }}/adminaddress/update/${id}`;
            if (nameInp) nameInp.value = name;
            if (phoneInp) phoneInp.value = phone;
            if (defCheck) defCheck.checked = (isDefault == 1);

            const api = await editAPI;
            if (!api) return;

            const parts = fullStr.split(',').map(s => s.trim());

            // 2. Nạp dữ liệu địa chỉ cũ đồng bộ (Tỉnh -> Huyện -> Xã)
            if (parts.length >= 4) {
                editModal.querySelector('.street-input').value = parts[0];
                
                const provinces = await api.loadP(parts[3]);
                const province = provinces ? provinces.find(p => p.name.trim().toLowerCase() === parts[3].toLowerCase()) : null;
                
                if (province) {
                    const districts = await api.loadD(province.code, parts[2]);
                    const district = districts ? districts.find(d => d.name.trim().toLowerCase() === parts[2].toLowerCase()) : null;
                    
                    if (district) {
                        await api.loadW(district.code, parts[1]);
                    }
                }
            } else {
                await api.loadP();
                editModal.querySelector('.street-input').value = fullStr;
            }
        });
    });
});
</script>

<style>
    .extra-small { font-size: 10px; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.05) !important; }
    .btn-white:hover { background-color: #f8fafc; }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .hover-primary:hover { color: #0d6efd !important; }
</style>

@include('admin.layouts.footer')