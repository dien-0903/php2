@include('admin.layouts.header')

<div class="container mt-4 text-dark mb-5 animate-slide-down">
    @if(isset($_SESSION['success']))
        <div class="alert alert-success border-0 shadow-sm mb-4 d-flex align-items-center rounded-4">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['success'] }}</div>
            @php unset($_SESSION['success']) @endphp
        </div>
    @endif
    @if(isset($_SESSION['error']))
        <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex align-items-center rounded-4">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $_SESSION['error'] }}</div>
            @php unset($_SESSION['error']) @endphp
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold uppercase tracking-tight text-slate-800">
                <i class="bi bi-geo-alt-fill text-primary me-2"></i>Sổ địa chỉ: <span class="text-primary">{{ $user['fullname'] }}</span>
            </h2>
            <div class="text-muted small mt-1 ms-4 ps-2">
                Tổng cộng: <span class="fw-bold text-dark">{{ $totalCount ?? 0 }}</span> địa chỉ 
                @if(isset($totalPages) && $totalPages > 1)
                    | Trang: <span class="text-primary fw-bold">{{ $currentPage }} / {{ $totalPages }}</span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary shadow-sm px-4 rounded-pill fw-bold transition-all hover-lift" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                <i class="bi bi-plus-lg me-1"></i> THÊM ĐỊA CHỈ
            </button>
            <a href="{{ rtrim(BASE_URL, '/') }}/adminuser/index" class="btn btn-outline-secondary shadow-sm px-4 rounded-pill fw-bold transition-all hover-lift">
                <i class="bi bi-arrow-left me-1"></i> QUAY LẠI
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-white border border-slate-100 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-start">
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
                    <tr class="{{ $addr['is_default'] ? 'bg-primary-subtle' : '' }} {{ !$isActive ? 'opacity-75 bg-light grayscale' : '' }}">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark fs-6">{{ $addr['recipient_name'] }}</div>
                            <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $addr['phone'] }}</div>
                        </td>
                        <td><div class="small text-dark lh-sm fw-medium">{{ $addr['address'] }}</div></td>
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
                    <tr>
                        <td colspan="5" class="text-center p-5 text-muted border-0">
                            <div class="display-6 opacity-25 mb-2"><i class="bi bi-geo-alt"></i></div>
                            Chưa có địa chỉ nào được lưu cho thành viên này.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($totalPages) && $totalPages > 1)
    <nav class="mt-4">
        <ul class="pagination justify-content-center gap-2">
            <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white shadow-hover" 
                   href="?page={{ $currentPage - 1 }}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>

            @for($i = 1; $i <= $totalPages; $i++)
                <li class="page-item {{ (int)$currentPage === (int)$i ? 'active' : '' }}">
                    <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold {{ (int)$currentPage === (int)$i ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark shadow-hover' }}" 
                       href="?page={{ $i }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor

            <li class="page-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
                <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white shadow-hover" 
                   href="?page={{ $currentPage + 1 }}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        </ul>
    </nav>
    @endif
</div>

<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ rtrim(BASE_URL, '/') }}/adminaddress/store" method="POST" id="addAddressForm" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="user_id" value="{{ $user['id'] }}">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">Thêm địa chỉ giao hàng</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Họ tên người nhận</label>
                    <input type="text" name="recipient_name" class="form-control rounded-3 py-2 border-slate-200" 
                           required value="{{ $user['fullname'] }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control rounded-3 py-2 border-slate-200" 
                           required value="{{ $user['phone'] ?? '' }}" placeholder="Ví dụ: 0912xxxxxx">
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <select class="form-select province-select rounded-3 border-slate-200" required>
                            <option value="" selected disabled>Tỉnh/Thành</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select district-select rounded-3 border-slate-200" required disabled>
                            <option value="" selected disabled>Quận/Huyện</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select ward-select rounded-3 border-slate-200" required disabled>
                            <option value="" selected disabled>Phường/Xã</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control street-input rounded-3 border-slate-200 py-2" required placeholder="Số nhà, tên đường...">
                </div>
                <input type="hidden" name="address" class="address-full-hidden">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input shadow-none" type="checkbox" name="is_default" value="1" id="defaultAdd">
                    <label class="form-check-label small fw-bold" for="defaultAdd">Đặt làm địa chỉ mặc định</label>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">LƯU ĐỊA CHỈ</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editAddressForm" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            <input type="hidden" name="user_id" value="{{ $user['id'] }}">
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa địa chỉ</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Họ tên người nhận</label>
                    <input type="text" name="recipient_name" id="address_edit_name" class="form-control rounded-3 py-2 border-slate-200" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Số điện thoại</label>
                    <input type="text" name="phone" id="address_edit_phone" class="form-control rounded-3 py-2 border-slate-200" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <select class="form-select province-select rounded-3 border-slate-200">
                            <option value="" selected disabled>Đang tải...</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select district-select rounded-3 border-slate-200" disabled>
                            <option value="" selected disabled>Quận/Huyện</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select ward-select rounded-3 border-slate-200" disabled>
                            <option value="" selected disabled>Phường/Xã</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control street-input rounded-3 border-slate-200 py-2" required>
                </div>
                <input type="hidden" name="address" class="address-full-hidden">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input shadow-none" type="checkbox" name="is_default" id="address_edit_default" value="1">
                    <label class="form-check-label small fw-bold" for="address_edit_default">Đặt làm mặc định</label>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold shadow-sm text-dark">CẬP NHẬT THAY ĐỔI</button>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.05) !important; }
    .bg-success-subtle { background-color: #e6ffed !important; }
    .bg-secondary-subtle { background-color: #f1f5f9 !important; }
    .grayscale { filter: grayscale(1); opacity: 0.7; }
    .hover-lift:hover { transform: translateY(-3px); }
    .shadow-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
    .transition-all { transition: all 0.3s ease; }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .extra-small { font-size: 10px; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const API_URL = 'https://provinces.open-api.vn/api/';

    async function initProvinceLogic(container) {
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
            } catch (err) { console.error(err); }
        }

        async function loadD(pCode, selectedName = null) {
            if (!pCode) return;
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
        }

        async function loadW(dCode, selectedName = null) {
            if (!dCode) return;
            wSel.disabled = false;
            wSel.innerHTML = '<option value="" selected disabled>Đang tải...</option>';
            const res = await fetch(`${API_URL}d/${dCode}?depth=2`);
            const data = await res.json();
            wSel.innerHTML = '<option value="" selected disabled>Phường/Xã</option>';
            data.wards.forEach(w => {
                const isSelected = (selectedName && w.name.trim().toLowerCase() === selectedName.trim().toLowerCase()) ? 'selected' : '';
                wSel.innerHTML += `<option value="${w.name}" ${isSelected}>${w.name}</option>`;
            });
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

        return { loadP, loadD, loadW };
    }

    const addModal = document.getElementById('addAddressModal');
    if (addModal) initProvinceLogic(addModal).then(api => api && api.loadP());

    const editModal = document.getElementById('editAddressModal');
    const editAPI = editModal ? initProvinceLogic(editModal) : null;

    document.querySelectorAll('.btn-edit-address').forEach(btn => {
        btn.addEventListener('click', async function() {
            const api = await editAPI;
            if (!api) return;

            const fullStr = this.dataset.full || "";
            const parts = fullStr.split(',').map(s => s.trim());

            document.getElementById('editAddressForm').action = `{{ rtrim(BASE_URL, '/') }}/adminaddress/update/${this.dataset.id}`;
            document.getElementById('address_edit_name').value = this.dataset.name;
            document.getElementById('address_edit_phone').value = this.dataset.phone;
            document.getElementById('address_edit_default').checked = (this.dataset.default == 1);

            if (parts.length >= 4) {
                editModal.querySelector('.street-input').value = parts[0];
                const provinces = await api.loadP(parts[3]);
                const province = provinces ? provinces.find(p => p.name.trim().toLowerCase() === parts[3].toLowerCase()) : null;
                if (province) {
                    const districts = await api.loadD(province.code, parts[2]);
                    const district = districts ? districts.find(d => d.name.trim().toLowerCase() === parts[2].toLowerCase()) : null;
                    if (district) await api.loadW(district.code, parts[1]);
                }
            } else {
                await api.loadP();
                editModal.querySelector('.street-input').value = fullStr;
            }
        });
    });
});
</script>

@include('admin.layouts.footer')