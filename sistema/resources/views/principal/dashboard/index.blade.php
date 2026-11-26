@extends('template.administrador')

@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen de operaciones y estadísticas')
@section('item_section', 'Inicio')
@section('route_section', route('dashboard.index'))
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
<div class="row g-3 mb-4">
    @foreach ([
        ['Ventas', $metrics['sales_total'], 'bi-cart-check', 'primary'],
        ['Ventas hoy', $metrics['sales_today'], 'bi-calendar-check', 'success'],
        ['Clientes', $metrics['customers'], 'bi-people', 'info'],
        ['Medicamentos', $metrics['medicines'], 'bi-capsule', 'warning'],
        ['Usuarios activos', $metrics['users'], 'bi-person-check', 'secondary'],
        ['Ingresos totales', 'S/ '.number_format($metrics['income'], 2), 'bi-cash-stack', 'danger'],
    ] as $card)
        <div class="col-12 col-sm-6 col-xl-4 col-xxl-2">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-{{ $card[3] }}-subtle text-{{ $card[3] }} d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                        <i class="bi {{ $card[2] }} fs-5"></i>
                    </div>
                    <div>
                        <div class="text-muted small">{{ $card[0] }}</div>
                        <div class="fs-5 fw-bold">{{ $card[1] }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>Ventas e ingresos mensuales</strong></div>
            <div class="card-body"><canvas id="salesChart" height="120"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white"><strong>Ingresos por categoría</strong></div>
            <div class="card-body"><canvas id="categoryChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Ventas de los últimos siete días</strong></div>
            <div class="card-body"><canvas id="sevenDaysChart" height="130"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Medicamentos por categoría</strong></div>
            <div class="card-body"><canvas id="medicineCategoryChart" height="130"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white"><strong>Medicamentos más vendidos</strong></div>
            <div class="card-body"><canvas id="topChart" height="130"></canvas></div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between"><strong>Stock bajo</strong><span class="badge bg-danger">{{ $lowStock->count() }}</span></div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover mb-0"><tbody>
                @forelse ($lowStock as $medicine)
                    <tr><td class="ps-3">{{ $medicine->name }}</td><td class="text-muted small">{{ $medicine->category?->name }}</td><td class="text-end pe-3"><span class="badge bg-danger-subtle text-danger">{{ $medicine->stock }} und.</span></td></tr>
                @empty
                    <tr><td class="text-muted p-3">Sin alertas de stock.</td></tr>
                @endforelse
            </tbody></table></div></div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Próximos a vencer</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover mb-0"><tbody>
            @forelse ($expirySoon as $medicine)
                <tr><td class="ps-3">{{ $medicine->name }}</td><td class="text-muted small">{{ $medicine->expiry_date->format('d/m/Y') }}</td><td class="text-end pe-3"><span class="badge bg-warning-subtle text-warning-emphasis">{{ $medicine->expiry_date->diffForHumans() }}</span></td></tr>
            @empty
                <tr><td class="text-muted p-3">No hay medicamentos próximos a vencer.</td></tr>
            @endforelse
        </tbody></table></div></div></div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Últimas ventas</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm table-hover mb-0"><tbody>
            @forelse ($latestSales as $sale)
                <tr><td class="ps-3"><a href="{{ route('sales.show', $sale) }}">Venta #{{ $sale->id }}</a></td><td>{{ $sale->customer?->name }}</td><td class="text-end pe-3 fw-semibold">S/ {{ number_format($sale->total, 2) }}</td></tr>
            @empty
                <tr><td class="text-muted p-3">Todavía no existen ventas.</td></tr>
            @endforelse
        </tbody></table></div></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/chart.umd.min.js') }}"></script>
<script>
const chartData = @json($chartData);
new Chart(document.getElementById('salesChart'), {type:'line',data:{labels:chartData.months,datasets:[{label:'Ventas',data:chartData.orders,borderColor:'#0d6efd',tension:.25,yAxisID:'y'},{label:'Ingresos (S/)',data:chartData.income,borderColor:'#198754',tension:.25,yAxisID:'y1'}]},options:{responsive:true,scales:{y:{beginAtZero:true},y1:{beginAtZero:true,position:'right',grid:{drawOnChartArea:false}}}}});
new Chart(document.getElementById('sevenDaysChart'), {type:'bar',data:{labels:chartData.sevenLabels,datasets:[{label:'Ventas',data:chartData.sevenOrders,backgroundColor:'#0d6efd'},{label:'Ingresos (S/)',data:chartData.sevenIncome,backgroundColor:'#198754'}]},options:{responsive:true,scales:{y:{beginAtZero:true}}}});
new Chart(document.getElementById('topChart'), {type:'bar',data:{labels:chartData.topNames,datasets:[{label:'Unidades vendidas',data:chartData.topSold,backgroundColor:'#0dcaf0'}]},options:{responsive:true,indexAxis:'y',scales:{x:{beginAtZero:true}}}});
new Chart(document.getElementById('categoryChart'), {type:'doughnut',data:{labels:chartData.categoryNames,datasets:[{data:chartData.categoryIncome,backgroundColor:['#0d6efd','#198754','#ffc107','#dc3545','#6f42c1','#20c997']}]},options:{responsive:true}});
new Chart(document.getElementById('medicineCategoryChart'), {type:'polarArea',data:{labels:chartData.medicineCategoryNames,datasets:[{data:chartData.medicineCategoryTotals,backgroundColor:['#0d6efd99','#19875499','#ffc10799','#dc354599','#6f42c199','#20c99799']}]},options:{responsive:true}});
</script>
@endpush