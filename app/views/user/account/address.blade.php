@include('user.layouts.header')

<div class="container py-5 text-dark min-vh-100 text-start">
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white animate-slide-down">
                <div class="text-center mb-4 pt-3">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm mx-auto fw-black" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ mb_strtoupper(mb_substr($user['fullname'] ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="fw-bold mb-0 text-truncate px-2">{{ $user['fullname'] ?? 'Thành viên' }}</h5>
                    <p class="text-muted small mb-0">{{ $user['email'] ?? '' }}</p>
                </div>
                
                <div class="list-group list-group-flush rounded-3 overflow-hidden border-top mt-3">
                    <a href="{{ rtrim(BASE_URL, '/') }}/user/profile" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="bi bi-person-bounding-box me-2"></i> Hồ sơ cá nhân
                    </a>
                    <a href="{{ rtrim(BASE_URL, '/') }}/order/history" class="list-group-item list-group-item-action border-0 py-3">
                        <i class="bi bi-bag-check me-2"></i> Lịch sử đơn hàng
                    </a>
                    <a href="{{ rtrim(BASE_URL, '/') }}/address/index" class="list-group-item list-group-item-action active border-0 py-3">
                        <i class="bi bi-geo-alt me-2"></i> Sổ địa chỉ
                    </a>
                    <hr class="my-1 opacity-10">
                    <a href="{{ rtrim(BASE_URL, '/') }}/auth/logout" class="list-group-item list-group-item-action border-0 py-3 text-danger fw-bold">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-black mb-0 uppercase border-start border-4 border-primary ps-3 tracking-tighter">Sổ địa chỉ của tôi</h4>
                    <p class="text-muted small mb-0 mt-1 ms-3">Bạn đang có tổng cộng {{ $totalCount ?? 0 }} địa chỉ.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm transition-all hover-lift" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="bi bi-plus-lg me-1"></i> Thêm địa chỉ mới
                </button>
            </div>

            <div class="row g-3">
                @forelse($addresses as $addr)
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden transition-all hover-lift {{ $addr['is_default'] ? 'border-start border-4 border-primary' : '' }}">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="fw-bold text-dark fs-5">{{ $addr['recipient_name'] }}</span>
                                        @if($addr['is_default'])
                                            <span class="badge bg-primary-subtle text-primary rounded-pill ms-3 extra-small fw-bold uppercase px-3 py-2">Mặc định</span>
                                        @endif
                                    </div>
                                    <div class="text-secondary mb-1 small">
                                        <i class="bi bi-telephone me-2 text-primary"></i>{{ $addr['phone'] }}
                                    </div>
                                    <div class="text-muted small">
                                        <i class="bi bi-geo-alt me-2 text-danger"></i>{{ $addr['address'] }}
                                    </div>
                                </div>
                                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        @if(!$addr['is_default'])
                                            <a href="{{ rtrim(BASE_URL, '/') }}/address/setDefault/{{ $addr['id'] }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Đặt mặc định</a>
                                        @endif
                                        <button class="btn btn-sm btn-warning rounded-pill px-3 fw-bold btn-edit-user-addr" 
                                            data-bs-toggle="modal" data-bs-target="#editAddressModal"
                                            data-id="{{ $addr['id'] }}"
                                            data-name="{{ $addr['recipient_name'] }}"
                                            data-phone="{{ $addr['phone'] }}"
                                            data-full="{{ $addr['address'] }}"
                                            data-default="{{ $addr['is_default'] }}">
                                            <i class="bi bi-pencil-square me-1"></i>Sửa
                                        </button>
                                        <a href="{{ rtrim(BASE_URL, '/') }}/address/destroy/{{ $addr['id'] }}" 
                                           class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" 
                                           onclick="return confirm('Xóa địa chỉ này khỏi danh sách?')">
                                            <i class="bi bi-trash3 me-1"></i>Xóa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm border border-dashed border-2">
                    <i class="bi bi-geo-alt display-1 text-muted opacity-25"></i>
                    <h5 class="text-muted mt-3 fw-bold">Chưa có địa chỉ nào được lưu</h5>
                </div>
                @endforelse
            </div>

            @php
                $currentPage = (int)($currentPage ?? 1);
                $totalPages = (int)($totalPages ?? 1);
                $baseUrl = rtrim(BASE_URL, '/') . '/address/index';
            @endphp

            @if($totalPages > 1)
            <nav class="mt-5">
                <ul class="pagination justify-content-center gap-2">
                    <li class="page-item {{ $currentPage <= 1 ? 'disabled' : '' }}">
                        <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white shadow-hover" 
                           href="{{ $baseUrl }}?page={{ $currentPage - 1 }}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>

                    @for($i = 1; $i <= $totalPages; $i++)
                        <li class="page-item {{ $currentPage === $i ? 'active' : '' }}">
                            <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold {{ $currentPage === $i ? 'bg-primary text-white shadow-primary' : 'bg-white text-dark shadow-hover' }}" 
                               href="{{ $baseUrl }}?page={{ $i }}">
                                {{ $i }}
                            </a>
                        </li>
                    @endfor

                    <li class="page-item {{ $currentPage >= $totalPages ? 'disabled' : '' }}">
                        <a class="page-link rounded-3 border-0 shadow-sm px-3 py-2 fw-bold text-dark bg-white shadow-hover" 
                           href="{{ $baseUrl }}?page={{ $currentPage + 1 }}">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ rtrim(BASE_URL, '/') }}/address/store" method="POST" id="userAddAddrForm" class="modal-content border-0 shadow-lg rounded-4 text-start">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">Thêm địa chỉ giao hàng</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Họ tên người nhận</label>
                    <input type="text" name="recipient_name" class="form-control rounded-3 py-2 bg-light border-0 shadow-none" 
                           required value="{{ $user['fullname'] ?? '' }}" placeholder="Nhập tên người nhận">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control rounded-3 py-2 bg-light border-0 shadow-none" 
                           required value="{{ $user['phone'] ?? '' }}" placeholder="Ví dụ: 0912345678">
                </div>
                <div class="row g-2 mb-3 text-start">
                    <div class="col-4">
                        <select class="form-select province-select rounded-3 border-0 bg-light shadow-none small" required>
                            <option value="" selected disabled>Tỉnh</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select district-select rounded-3 border-0 bg-light shadow-none small" required disabled>
                            <option value="" selected disabled>Huyện</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select ward-select rounded-3 border-0 bg-light shadow-none small" required disabled>
                            <option value="" selected disabled>Xã</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <input type="text" class="form-control street-input rounded-3 border-0 bg-light shadow-none" required placeholder="Số nhà, tên đường...">
                </div>
                <input type="hidden" name="address_full" class="address-full-hidden">
                <div class="form-check form-switch text-start">
                    <input class="form-check-input shadow-none" type="checkbox" name="is_default" value="1" id="userDefaultAdd">
                    <label class="form-check-label small fw-bold" for="userDefaultAdd">Đặt làm địa chỉ mặc định</label>
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
        <form action="" method="POST" id="userEditAddrForm" class="modal-content border-0 shadow-lg rounded-4 text-start">
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">Chỉnh sửa địa chỉ</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark text-start">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Họ tên người nhận</label>
                    <input type="text" name="recipient_name" id="user_edit_name" class="form-control rounded-3 py-2 bg-light border-0 shadow-none" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary uppercase">Số điện thoại</label>
                    <input type="text" name="phone" id="user_edit_phone" class="form-control rounded-3 py-2 bg-light border-0 shadow-none" required>
                </div>
                <div class="row g-2 mb-3 text-start">
                    <div class="col-4">
                        <select class="form-select province-select rounded-3 border-0 bg-light shadow-none small" required>
                            <option value="" selected disabled>Đang tải...</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select district-select rounded-3 border-0 bg-light shadow-none small" required disabled>
                            <option value="" selected disabled>Huyện</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <select class="form-select ward-select rounded-3 border-0 bg-light shadow-none small" required disabled>
                            <option value="" selected disabled>Xã</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <input type="text" class="form-control street-input rounded-3 border-0 bg-light shadow-none" required placeholder="Số nhà, tên đường...">
                </div>
                <input type="hidden" name="address_full" class="address-full-hidden">
                <div class="form-check form-switch text-start">
                    <input class="form-check-input shadow-none" type="checkbox" name="is_default" value="1" id="user_edit_default">
                    <label class="form-check-label small fw-bold" for="user_edit_default">Đặt làm địa chỉ mặc định</label>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold shadow-sm text-dark">CẬP NHẬT</button>
            </div>
        </form>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .extra-small { font-size: 10px; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .shadow-hover:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important; }
    .transition-all { transition: all 0.3s ease; }
    .shadow-primary { box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .bg-primary-subtle { background-color: #e7f1ff !important; }
    .tracking-tighter { letter-spacing: -1.5px; }
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
            } catch (err) { console.error("Lỗi API Tỉnh:", err); }
        }

        async function loadD(pCode, selectedName = null) {
            if (!pCode) return;
            dSel.disabled = false;
            dSel.innerHTML = '<option value="" selected disabled>Đang tải...</option>';
            try {
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
            wSel.disabled = false;
            wSel.innerHTML = '<option value="" selected disabled>Đang tải...</option>';
            try {
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

        return { loadP, loadD, loadW };
    }

    const addModal = document.getElementById('addAddressModal');
    if (addModal) initProvinceLogic(addModal).then(api => api && api.loadP());

    const editModal = document.getElementById('editAddressModal');
    const editAPI = editModal ? initProvinceLogic(editModal) : null;

    document.querySelectorAll('.btn-edit-user-addr').forEach(btn => {
        btn.addEventListener('click', async function() {
            const api = await editAPI;
            if (!api) return;

            const fullStr = this.dataset.full || "";
            const parts = fullStr.split(',').map(s => s.trim());

            const editForm = document.getElementById('userEditAddrForm');
            if (editForm) editForm.action = `{{ rtrim(BASE_URL, '/') }}/address/update/${this.dataset.id}`;
            document.getElementById('user_edit_name').value = this.dataset.name;
            document.getElementById('user_edit_phone').value = this.dataset.phone;
            document.getElementById('user_edit_default').checked = (this.dataset.default == 1);

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

@include('user.layouts.footer')