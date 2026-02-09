@include('admin.layouts.header')

@php
    $maxRevenue = 0;
    foreach($chartData as $day) {
        if($day['revenue'] > $maxRevenue) $maxRevenue = $day['revenue'];
    }
    
    $scaleMax = ($maxRevenue > 0) ? $maxRevenue : 1000000; 

    $totalRev = $overview['total_revenue'] ?? 0;
    $totalOrd = $overview['total_orders'] ?? 0;
    $pending  = $overview['pending_count'] ?? 0;
    $canceled = $overview['canceled_count'] ?? 0;
@endphp

<div class="container mt-4 text-dark text-start animate-slide-down mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-black uppercase tracking-tighter mb-0">
                <i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Thống kê doanh thu
            </h2>
            <p class="text-muted small mb-0">Phân tích hiệu suất bán hàng 7 ngày gần nhất</p>
        </div>
        <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Xuất báo cáo
        </button>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white h-100 shadow-primary">
                <div class="small fw-bold uppercase opacity-75">Doanh thu thực tế</div>
                <div class="h3 fw-black mb-0 mt-1">{{ number_format($totalRev) }}đ</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border border-slate-100 h-100">
                <div class="small fw-bold uppercase text-muted">Tổng số đơn hàng</div>
                <div class="h3 fw-black mb-0 mt-1 text-dark">{{ $totalOrd }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border border-slate-100 h-100">
                <div class="small fw-bold uppercase text-warning">Đang chờ xử lý</div>
                <div class="h3 fw-black mb-0 mt-1 text-dark">{{ $pending }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border border-slate-100 h-100">
                <div class="small fw-bold uppercase text-danger">Đơn đã hủy</div>
                <div class="h3 fw-black mb-0 mt-1 text-dark">{{ $canceled }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 border border-slate-100 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-2">
                    <h6 class="fw-bold uppercase text-muted small mb-0">Biểu đồ tăng trưởng tuần này</h6>
                    <span class="badge bg-primary text-white border-0 extra-small fw-bold px-3">VNĐ / NGÀY</span>
                </div>
                
                <div class="chart-container-inner position-relative" style="height: 350px; background-image: linear-gradient(#f8fafc 1px, transparent 1px); background-size: 100% 50px;">
                    
                    <div class="d-flex align-items-end justify-content-around h-100 px-2">
                        @foreach($chartData as $day)
                            @php 
                                $percent = ($day['revenue'] / $scaleMax) * 100;
                                
                                if($day['revenue'] > 0 && $percent < 8) $percent = 8;
                                
                                $isToday = ($day['date'] == date('Y-m-d'));
                                $hasRevenue = $day['revenue'] > 0;
                            @endphp
                            
                            <div class="text-center h-100 d-flex flex-column justify-content-end align-items-center position-relative group" style="width: 14%;">
                                
                                @if($hasRevenue)
                                <div class="revenue-label-top fw-black text-primary mb-2 animate-bounce-in" style="font-size: 9px;">
                                    {{ number_format($day['revenue'] / 1000, 0) }}k
                                </div>
                                @endif

                                <div class="chart-bar rounded-top transition-all {{ $isToday ? 'bg-primary' : ($hasRevenue ? 'bg-primary-light' : 'bg-slate-100') }}" 
                                     style="width: 100%; max-width: 40px; height: {{ $hasRevenue ? $percent : '2' }}%; min-height: {{ $hasRevenue ? '10px' : '2px' }};"
                                     data-bs-toggle="tooltip" 
                                     title="{{ date('d/m/Y', strtotime($day['date'])) }}: {{ number_format($day['revenue']) }}đ">
                                     
                                     <div class="bar-shimmer"></div>
                                </div>

                                <div class="mt-3 text-center">
                                    <div class="extra-small fw-bold {{ $isToday ? 'text-primary' : 'text-muted' }}" style="font-size: 10px;">
                                        {{ date('d/m', strtotime($day['date'])) }}
                                    </div>
                                    @if($isToday)
                                        <span class="badge bg-primary rounded-pill p-1 shadow-sm mt-1" style="font-size: 7px; display: block;">HÔM NAY</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="extra-small text-muted italic mb-0">
                                <i class="bi bi-info-circle-fill me-1"></i> Số liệu dựa trên các đơn hàng đã <strong>Hoàn thành</strong>.
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span class="extra-small fw-bold text-dark uppercase">Đỉnh điểm: </span>
                            <span class="fw-black text-danger ms-1">{{ number_format($maxRevenue) }}đ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100 border border-slate-100">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
                    <h6 class="fw-bold uppercase text-muted small mb-0">Sản phẩm bán chạy nhất</h6>
                    <span class="text-warning"><i class="bi bi-award-fill fs-5"></i></span>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($topProducts as $p)
                    <div class="list-group-item px-0 py-3 border-0 border-bottom border-dashed transition-all hover-bg-light rounded-2">
                        <div class="fw-bold text-dark small text-truncate mb-1" title="{{ $p['product_name'] }}">
                            {{ $p['product_name'] }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary-subtle text-primary border-0 rounded-pill px-3 extra-small fw-bold">
                                {{ $p['total_qty'] }} SP
                            </span>
                            <span class="text-danger fw-black small">{{ number_format($p['total_money']) }}đ</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted small italic bg-light rounded-4">
                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                        Dữ liệu đang được cập nhật...
                    </div>
                    @endforelse
                </div>
                <div class="mt-auto pt-4">
                    <a href="{{ rtrim(BASE_URL, '/') }}/adminproduct/index" class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-bold py-2">
                        XEM KHO HÀNG <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .tracking-tighter { letter-spacing: -1px; }
    .extra-small { font-size: 11px; }
    .border-dashed { border-style: dashed !important; }
    
    .bg-primary-light { background-color: #3b82f6 !important; opacity: 0.7; }
    .bg-primary-subtle { background-color: #eef2ff !important; color: #4338ca !important; }
    .bg-slate-50 { background-color: #f8fafc; }
    
    .chart-bar { 
        position: relative; 
        cursor: pointer; 
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
    .chart-bar:hover { 
        filter: brightness(1.1); 
        transform: scaleX(1.15) translateY(-5px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
    }
    
    .bar-shimmer {
        position: absolute;
        top: 0;
        left: 0;
        width: 40%;
        height: 100%;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 4px 0 0 0;
    }

    .shadow-primary { box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4) !important; }
    .hover-bg-light:hover { background-color: #f8fafc; padding-left: 8px !important; padding-right: 8px !important; }

    @keyframes bounceIn { 
        from { opacity: 0; transform: translateY(10px); } 
        to { opacity: 1; transform: translateY(0); } 
    }
    .animate-bounce-in { animation: bounceIn 0.6s ease-out; }

    @media print {
        .navbar, .btn, .breadcrumb, .mt-auto { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #eee !important; }
        body { background: white !important; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@include('admin.layouts.footer')