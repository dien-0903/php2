@php
    if (session_status() === PHP_SESSION_NONE) session_start();
    
    $cart = $_SESSION['cart'] ?? [];
    
    if (!isset($subtotal)) {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
    }

    if (!isset($coupon)) {
        $coupon = $_SESSION['coupon'] ?? null;
    }

    if (!isset($discount)) {
        $discount = 0;
        if ($coupon) {
            $discount = ($coupon['type'] === 'percent') ? ($subtotal * $coupon['value'] / 100) : $coupon['value'];
            if ($discount > $subtotal) $discount = $subtotal;
        }
    }

    if (!isset($total)) {
        $total = $subtotal - $discount;
        if ($total < 0) $total = 0;
    }
@endphp

@include('user.layouts.header')

<div class="container py-5 text-dark">
    <h2 class="fw-black mb-4 uppercase text-center"><i class="bi bi-shield-check text-primary me-2"></i>Thanh toán đơn hàng</h2>

    <form action="{{ BASE_URL }}/order/placeOrder" method="POST" id="checkoutForm">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                    <h5 class="fw-bold mb-4 border-start border-4 border-primary ps-3 text-start">THÔNG TIN GIAO HÀNG</h5>
                    
                    @if(!empty($addresses))
                        <div class="mb-4 text-start">
                            <label class="form-label small fw-bold text-muted uppercase">Chọn địa chỉ đã lưu:</label>
                            <div class="row g-3">
                                @foreach($addresses as $addr)
                                <div class="col-md-6 text-start">
                                    <input type="radio" class="btn-check addr-radio" name="saved_address" id="addr-{{ $addr['id'] }}" 
                                           data-name="{{ $addr['recipient_name'] }}" 
                                           data-phone="{{ $addr['phone'] }}" 
                                           data-full="{{ $addr['address'] }}"
                                           {{ $addr['is_default'] ? 'checked' : '' }}>
                                    <label class="btn btn-outline-light text-dark w-100 rounded-4 p-3 text-start border shadow-sm h-100" for="addr-{{ $addr['id'] }}">
                                        <div class="fw-bold small">{{ $addr['recipient_name'] }}</div>
                                        <div class="extra-small text-muted mb-2">{{ $addr['phone'] }}</div>
                                        <div class="extra-small lh-sm text-secondary">{{ $addr['address'] }}</div>
                                    </label>
                                </div>
                                @endforeach
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check addr-radio" name="saved_address" id="addr-new" value="new">
                                    <label class="btn btn-outline-light text-dark w-100 rounded-4 p-3 text-start border shadow-sm h-100 d-flex align-items-center justify-content-center" for="addr-new">
                                        <div class="fw-bold small"><i class="bi bi-plus-circle me-1"></i> Dùng địa chỉ mới</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="recipient-info-box">
                        <div class="row">
                            <div class="col-md-6 mb-3 text-start">
                                <label class="form-label small fw-bold">Họ tên người nhận</label>
                                <input type="text" name="recipient_name" id="r_name" class="form-control rounded-3 py-2" required placeholder="Nhập tên người nhận">
                            </div>
                            <div class="col-md-6 mb-3 text-start">
                                <label class="form-label small fw-bold">Số điện thoại</label>
                                <input type="text" name="phone" id="r_phone" class="form-control rounded-3 py-2" required placeholder="Nhập số điện thoại">
                            </div>
                        </div>

                        <div id="new-address-picker" style="{{ !empty($addresses) ? 'display:none;' : '' }}">
                            <div class="row g-2 mb-3">
                                <div class="col-md-4">
                                    <select class="form-select rounded-3" id="province"><option value="" selected disabled>Tỉnh/Thành</option></select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select rounded-3" id="district" disabled><option value="" selected disabled>Quận/Huyện</option></select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select rounded-3" id="ward" disabled><option value="" selected disabled>Phường/Xã</option></select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <input type="text" id="street" class="form-control rounded-3" placeholder="Số nhà, tên đường...">
                            </div>
                        </div>
                        <input type="hidden" name="address_full" id="address_full">
                        <div class="mb-0 text-start">
                            <label class="form-label small fw-bold">Ghi chú đơn hàng (nếu có)</label>
                            <textarea name="note" class="form-control rounded-3" rows="2" placeholder="Ví dụ: Giao giờ hành chính..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-start">
                    <h5 class="fw-bold mb-3 border-start border-4 border-success ps-3 uppercase small">Ưu đãi giảm giá</h5>
                    @if(isset($coupon))
                        <div class="d-flex justify-content-between align-items-center p-3 bg-success-subtle border border-success-subtle rounded-4 animate-fade-in">
                            <div>
                                <span class="fw-bold text-success"><i class="bi bi-ticket-perforated-fill me-2"></i>{{ $coupon['code'] }}</span>
                                <div class="extra-small text-success mt-1">Đã áp dụng: -{{ number_format($discount) }}đ</div>
                            </div>
                            <a href="{{ BASE_URL }}/cart/removeCoupon" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">GỠ BỎ</a>
                        </div>
                    @else
                        <div class="input-group">
                            <input type="text" form="couponForm" name="coupon_code" class="form-control rounded-start-pill border-slate-200 shadow-none px-3" placeholder="Nhập mã voucher tại đây...">
                            <button type="submit" form="couponForm" class="btn btn-dark rounded-end-pill px-4 fw-bold">ÁP DỤNG</button>
                        </div>
                        <p class="extra-small text-muted mt-2 ps-2">Bạn có thể lấy mã tại <a href="{{ BASE_URL }}/coupon/index" target="_blank" class="text-primary text-decoration-none fw-bold">Kho Voucher MD</a></p>
                    @endif
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top text-start" style="top: 100px;">
                    <h5 class="fw-bold mb-4 border-start border-4 border-danger ps-3">TÓM TẮT ĐƠN HÀNG</h5>
                    
                    <div class="mb-4 overflow-auto" style="max-height: 250px;">
                        @foreach($cart as $item)
                        <div class="d-flex align-items-center mb-3">
                            <img src="http://localhost/PHP2/public/uploads/products/{{ $item['image'] ?: 'default.jpg' }}" class="rounded border" width="50" height="50" style="object-fit: cover;" onerror="this.src='https://placehold.co/100x100?text=SP'">
                            <div class="ms-3 flex-grow-1">
                                <div class="fw-bold small text-truncate" style="max-width: 180px;">{{ $item['name'] }}</div>
                                <div class="extra-small text-muted">{{ $item['variant_info'] ?? 'Mặc định' }}</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold small">{{ number_format($item['price']) }}đ</div>
                                <div class="extra-small text-muted">x{{ $item['quantity'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <hr class="opacity-10">
                    
                    <div class="d-flex justify-content-between mb-2 small text-muted">
                        <span>Tạm tính:</span>
                        <span class="fw-bold text-dark">{{ number_format($subtotal) }}đ</span>
                    </div>
                    @if($discount > 0)
                    <div class="d-flex justify-content-between mb-2 small text-success">
                        <span>Giảm giá ({{ $coupon['code'] }}):</span>
                        <span class="fw-bold">-{{ number_format($discount) }}đ</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between mb-3 small text-muted">
                        <span>Phí vận chuyển:</span>
                        <span class="text-success fw-bold">Miễn phí</span>
                    </div>
                    
                    <div class="bg-light rounded-4 p-3 mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold small text-dark">TỔNG THANH TOÁN:</span>
                            <span class="h4 fw-black text-danger mb-0">{{ number_format($total) }}đ</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold uppercase text-muted">Phương thức thanh toán:</label>
                        <div class="form-check border rounded-4 p-3 mb-2 cursor-pointer shadow-sm">
                            <input class="form-check-input ms-0 me-2 shadow-none" type="radio" name="payment_method" id="pay-cod" value="cod" checked>
                            <label class="form-check-label fw-bold small" for="pay-cod">
                                <i class="bi bi-cash me-2 text-warning"></i>Thanh toán khi nhận hàng (COD)
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg">
                        XÁC NHẬN ĐẶT HÀNG <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <form id="couponForm" action="{{ BASE_URL }}/cart/applyCoupon" method="POST" class="d-none"></form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addrRadios = document.querySelectorAll('.addr-radio');
    const newAddrPicker = document.getElementById('new-address-picker');
    const rName = document.getElementById('r_name');
    const rPhone = document.getElementById('r_phone');
    const addrFullInput = document.getElementById('address_full');
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const streetInput = document.getElementById('street');

    fetch('https://provinces.open-api.vn/api/p/')
        .then(res => res.json())
        .then(data => data.forEach(p => {
            provinceSelect.innerHTML += `<option value="${p.name}" data-code="${p.code}">${p.name}</option>`;
        }));

    provinceSelect.addEventListener('change', function() {
        const code = this.options[this.selectedIndex].getAttribute('data-code');
        districtSelect.disabled = false;
        districtSelect.innerHTML = '<option value="" selected disabled>Chọn Quận / Huyện</option>';
        fetch(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
            .then(res => res.json())
            .then(data => data.districts.forEach(d => {
                districtSelect.innerHTML += `<option value="${d.name}" data-code="${d.code}">${d.name}</option>`;
            }));
    });

    districtSelect.addEventListener('change', function() {
        const code = this.options[this.selectedIndex].getAttribute('data-code');
        wardSelect.disabled = false;
        wardSelect.innerHTML = '<option value="" selected disabled>Chọn Phường / Xã</option>';
        fetch(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
            .then(res => res.json())
            .then(data => data.wards.forEach(w => {
                wardSelect.innerHTML += `<option value="${w.name}">${w.name}</option>`;
            }));
    });

    function updateFields() {
        const selected = document.querySelector('.addr-radio:checked');
        if (selected && selected.value !== 'new') {
            newAddrPicker.style.display = 'none';
            rName.value = selected.dataset.name;
            rPhone.value = selected.dataset.phone;
            addrFullInput.value = selected.dataset.full;
        } else {
            newAddrPicker.style.display = 'block';
            rName.value = ''; rPhone.value = ''; addrFullInput.value = '';
        }
    }

    if(addrRadios.length > 0) {
        addrRadios.forEach(r => r.addEventListener('change', updateFields));
        updateFields();
    }

    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const selected = document.querySelector('.addr-radio:checked');
        if (!selected || selected.value === 'new') {
            const p = provinceSelect.value; const d = districtSelect.value; const w = wardSelect.value; const s = streetInput.value.trim();
            if(!p || !d || !w || !s) { alert('Vui lòng nhập đầy đủ địa chỉ mới!'); e.preventDefault(); return; }
            addrFullInput.value = `${s}, ${w}, ${d}, ${p}`;
        }
    });
});
</script>

@include('user.layouts.footer')