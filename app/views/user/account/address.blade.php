@include('user.layouts.header')

<div class="container py-5 text-dark min-vh-100">
    <div class="row g-4">
        <!-- SIDEBAR ĐIỀU HƯỚNG (BỎ ẢNH ĐẠI DIỆN THEO YÊU CẦU) -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white animate-slide-down">
                <div class="text-center mb-4 pt-3">
                    <div class="position-relative d-inline-block mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm mx-auto fw-black" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ mb_strtoupper(mb_substr($user['fullname'], 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="fw-bold mb-0 text-truncate px-2">{{ $user['fullname'] }}</h5>
                    <p class="text-muted small mb-0">{{ $user['email'] }}</p>
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

        <!-- NỘI DUNG CHÍNH -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-black mb-0 uppercase border-start border-4 border-primary ps-3">Sổ địa chỉ của tôi</h4>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">
                    <i class="bi bi-plus-lg me-1"></i> Thêm địa chỉ mới
                </button>
            </div>

            <!-- DANH SÁCH ĐỊA CHỈ -->
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
                                            <span class="badge bg-primary-subtle text-primary rounded-pill ms-3 extra-small fw-bold uppercase">Mặc định</span>
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
                                            <a href="{{ rtrim(BASE_URL, '/') }}/address/setDefault/{{ $addr['id'] }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Mặc định</a>
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
                                           class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" 
                                           onclick="return confirm('Xóa địa chỉ này?')">
                                            <i class="bi bi-trash3 me-1"></i>Xóa
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5 bg-white rounded-4 shadow-sm border border-dashed">
                    <i class="bi bi-geo-alt display-1 text-muted opacity-25"></i>
                    <h5 class="text-muted mt-3 fw-bold">Chưa có địa chỉ nào được lưu</h5>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- MODAL THÊM MỚI -->
<div class="modal fade" id="addAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ rtrim(BASE_URL, '/') }}/address/store" method="POST" id="userAddAddrForm" class="modal-content border-0 shadow-lg rounded-4 text-start">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">Thêm địa chỉ giao hàng</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-secondary uppercase">Họ tên người nhận</label>
                    <input type="text" name="recipient_name" class="form-control rounded-3 py-2 bg-light border-0 shadow-none" 
                           required value="{{ $user['fullname'] }}" placeholder="Nhập tên người nhận">
                </div>
                <div class="mb-3 text-start">
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

<!-- MODAL SỬA -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" id="userEditAddrForm" class="modal-content border-0 shadow-lg rounded-4 text-start">
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">Chỉnh sửa địa chỉ</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-dark">
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-secondary uppercase">Họ tên người nhận</label>
                    <input type="text" name="recipient_name" id="user_edit_name" class="form-control rounded-3 py-2 bg-light border-0 shadow-none" required>
                </div>
                <div class="mb-3 text-start">
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
                    <label class="form-check-label small fw-bold" for="user_edit_default">Đặt làm mặc định</label>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-warning w-100 rounded-pill py-2 fw-bold shadow-sm">CẬP NHẬT</button>
            </div>
        </form>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .extra-small { font-size: 10px; }
    .hover-lift:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .transition-all { transition: all 0.3s ease; }
    .animate-slide-down { animation: slideDown 0.4s ease-out; }
    @keyframes slideDown { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .bg-primary-subtle { background-color: #e7f1ff !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const API_URL = 'https://provinces.open-api.vn/api/';

    // Logic nạp dữ liệu API Tỉnh/Huyện/Xã chuẩn xác
    async function initProvinceLogic(container) {
        const pSel = container.querySelector('.province-select');
        const dSel = container.querySelector('.district-select');
        const wSel = container.querySelector('.ward-select');
        const street = container.querySelector('.street-input');
        const hidden = container.querySelector('.address-full-hidden');
        const form = container.querySelector('form'); // FIX: Tìm form bên trong container

        // Kiểm tra an toàn trước khi gán sự kiện
        if (!pSel || !dSel || !wSel || !street || !hidden || !form) {
            return null;
        }

        async function loadP(selectedName = null) {
            try {
                const res = await fetch(API_URL + 'p/');
                if (!res.ok) throw new Error('API Tỉnh thất bại');
                const data = await res.json();
                
                pSel.innerHTML = '<option value="" selected disabled>Tỉnh/Thành</option>';
                data.forEach(p => {
                    const isSelected = (selectedName && p.name.trim().toLowerCase() === selectedName.trim().toLowerCase()) ? 'selected' : '';
                    pSel.innerHTML += `<option value="${p.name}" data-code="${p.code}" ${isSelected}>${p.name}</option>`;
                });
                return data;
            } catch (err) { 
                console.error("Lỗi API Tỉnh:", err);
                pSel.innerHTML = '<option value="" disabled>Lỗi tải dữ liệu</option>';
            }
        }

        async function loadD(pCode, selectedName = null) {
            if (!pCode) return;
            try {
                dSel.disabled = false;
                dSel.innerHTML = '<option value="" selected disabled>Đang tải...</option>';
                const res = await fetch(`${API_URL}p/${pCode}?depth=2`);
                if (!res.ok) throw new Error('API Huyện thất bại');
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
                if (!res.ok) throw new Error('API Xã thất bại');
                const data = await res.json();
                
                wSel.innerHTML = '<option value="" selected disabled>Phường/Xã</option>';
                data.wards.forEach(w => {
                    const isSelected = (selectedName && w.name.trim().toLowerCase() === selectedName.trim().toLowerCase()) ? 'selected' : '';
                    wSel.innerHTML += `<option value="${w.name}" ${isSelected}>${w.name}</option>`;
                });
            } catch (err) { console.error("Lỗi API Xã:", err); }
        }

        // Sự kiện đổi Tỉnh
        pSel.addEventListener('change', async function() {
            const code = this.options[this.selectedIndex].dataset.code;
            wSel.innerHTML = '<option value="" selected disabled>Phường/Xã</option>';
            wSel.disabled = true;
            await loadD(code);
        });

        // Sự kiện đổi Huyện
        dSel.addEventListener('change', async function() {
            const code = this.options[this.selectedIndex].dataset.code;
            await loadW(code);
        });

        // Gộp chuỗi khi gửi form
        form.addEventListener('submit', function() {
            if (pSel.value && dSel.value && wSel.value && street.value) {
                hidden.value = `${street.value.trim()}, ${wSel.value}, ${dSel.value}, ${pSel.value}`;
            }
        });

        return { loadP, loadD, loadW };
    }

    // Khởi tạo Modal Thêm
    const addModal = document.getElementById('addAddressModal');
    if (addModal) {
        initProvinceLogic(addModal).then(api => {
            if (api) api.loadP();
        });
    }

    // Khởi tạo Modal Sửa
    const editModal = document.getElementById('editAddressModal');
    const editAPI = editModal ? initProvinceLogic(editModal) : null;

    // Xử lý nút Sửa
    document.querySelectorAll('.btn-edit-user-addr').forEach(btn => {
        btn.addEventListener('click', async function() {
            const api = await editAPI;
            if (!api) return;

            const fullStr = this.dataset.full || "";
            const parts = fullStr.split(',').map(s => s.trim());

            // Gán dữ liệu cơ bản
            const editForm = document.getElementById('userEditAddrForm');
            if (editForm) {
                editForm.action = `{{ rtrim(BASE_URL, '/') }}/address/update/${this.dataset.id}`;
            }
            document.getElementById('user_edit_name').value = this.dataset.name;
            document.getElementById('user_edit_phone').value = this.dataset.phone;
            document.getElementById('user_edit_default').checked = (this.dataset.default == 1);

            // Xử lý nạp địa chỉ cũ (Đường, Xã, Huyện, Tỉnh)
            if (parts.length >= 4) {
                const oldStreet = parts[0];
                const oldWard = parts[1];
                const oldDistrict = parts[2];
                const oldProvince = parts[3];

                editModal.querySelector('.street-input').value = oldStreet;
                
                // 1. Nạp Tỉnh và tìm code
                const provinces = await api.loadP(oldProvince);
                const province = provinces ? provinces.find(p => p.name.trim().toLowerCase() === oldProvince.toLowerCase()) : null;
                
                if (province) {
                    // 2. Nạp Huyện và tìm code
                    const districts = await api.loadD(province.code, oldDistrict);
                    const district = districts ? districts.find(d => d.name.trim().toLowerCase() === oldDistrict.toLowerCase()) : null;
                    
                    if (district) {
                        // 3. Nạp Xã
                        await api.loadW(district.code, oldWard);
                    }
                }
            } else {
                // Fallback nếu chuỗi không chuẩn (người dùng nhập tay trước đó)
                await api.loadP();
                editModal.querySelector('.street-input').value = fullStr;
            }
        });
    });
});
</script>

@include('user.layouts.footer')