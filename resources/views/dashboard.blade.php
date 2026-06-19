@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">📊 لوحة الإحصائيات العامة</h2>
        <span class="badge bg-soft-primary text-primary p-2">{{ date('Y-m-d') }}</span>
    </div>
 <div class="row">
     <div class="col-12 my-3">
         <div class="card bg-primary text-white w-100 py-3 border-0 shadow-sm text-center">
             <div class="card-body">
                 <i class="bi bi-graph-up-arrow fs-2 d-block mb-2"></i>
                 <span class="d-block small opacity-75">صافي الربح الحالي</span>
                 <h4 class="fw-bold mb-0">{{ number_format($netProfit) }} <small class="fs-6">ج.س</small></h4>
             </div>
         </div>
     </div>
         {{--  --}}
{{-- <div class="col-md-4 mb-4">
    <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(45deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1 opacity-75">رواتب شهر {{ now()->translatedFormat('F') }}</h6>
                    <h3 class="fw-bold mb-0">{{ number_format($monthlySalaryRequirement) }} ج.س</h3>
                </div>
                <div class="fs-1 opacity-50">
                    <i class="bi bi-cash-coin"></i>
                </div>
            </div>
            
            <hr class="my-3 opacity-25">
            
            <div class="row text-center g-0">
                <div class="col-6 border-end border-white border-opacity-25">
                    <small class="d-block opacity-75">تم صرفه</small>
                    <span class="fw-bold text-success">{{ number_format($paidSalariesThisMonth) }}</span>
                </div>
                <div class="col-6">
                    <small class="d-block opacity-75">متبقي</small>
                    <span class="fw-bold text-danger">{{ number_format($remainingSalaries) }}</span>
                </div>
            </div>
        </div>
    </div>
</div> --}}
        {{--  --}}
</div>   
  <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="opacity-75">إجمالي المعلمين</small>
                            <h3 class="fw-bold mb-0">{{ $teachersCount }}</h3>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
                
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="opacity-75"> رواتب المعلمين في الشهر</small>
                            <h3 class="fw-bold mb-0">{{ number_format($monthlySalaryRequirement) }}</h3>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
     
<div class="col-md-3">
<div class="card border-0 shadow-sm text-white" style="background-color: #28a745;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <p class="mb-1 small">رواتب تم صرفها  <i class="bi bi-check-circle-fill "></i></p>
               
                <h4 class="fw-bold mb-0">{{ number_format($paidSalariesThisMonth) }} <small class="fs-6 fw-light">ج.س</small></h4>
            </div>
            {{-- الحركة الذكية هنا: دائرة صغيرة توضح العدد --}}
            <div class="text-end">
                <span class="badge rounded-pill bg-white text-success shadow-sm">
                     {{ $paidTeachersCount }} / {{ $teachersCount }} معلماً
                </span>
            </div>
        </div>
        
        {{-- شريط تقدم صغير (Progress Bar) لإضافة لمسة جمالية --}}
        {{-- @php 
            $percent = $teachersCount > 0 ? ($paidTeachersCount / $teachersCount) * 100 : 0; 
        @endphp
        <div class="progress mt-3" style="height: 5px; background: rgba(255,255,255,0.2);">
            <div class="progress-bar bg-white" role="progressbar" style="width: {{ $percent }}%"></div>
        </div> --}}
    </div>
</div>
</div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="opacity-75">المتبقي هذا الشهر</small>
                            <h3 class="fw-bold mb-0">{{ $remainingSalaries }}</h3>
                        </div>
                        <i class="bi bi-exclamation-triangle-fill fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="opacity-75">إجمالي الطلاب</small>
                            <h3 class="fw-bold mb-0">{{ $stats['total_students'] }}</h3>
                        </div>
                        <i class="bi bi-people fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
                
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="opacity-75">المحصل فعلياً</small>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['total_collected']) }}</h3>
                        </div>
                        <i class="bi bi-cash-stack fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
     

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="opacity-75">المتبقي طرف الطلاب</small>
                            <h3 class="fw-bold mb-0">{{ number_format($stats['expected_total'] - $stats['total_collected']) }}</h3>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="opacity-75">عدد الفصول</small>
                            <h3 class="fw-bold mb-0">{{ $stats['total_classes'] }}</h3>
                        </div>
                        <i class="bi bi-door-open fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">📉 حالة سداد الطلاب</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="paymentChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold">⚡ وصول سريع</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('students.index') }}" class="btn btn-light w-100 py-4 border">
                                <i class="bi bi-person-lines-fill fs-2 d-block mb-2 text-primary"></i>
                                إدارة الطلاب
                            </a>
                        </div>

                        <div class="col-6">
                            <a href="{{ route('classrooms.stats') }}" class="btn btn-light w-100 py-4 border">
                                <i class="bi bi-building fs-2 d-block mb-2 text-success"></i>
                                إحصائيات الفصول
                            </a>
                        </div>

                        <div class="col-6">
                                 <a href="{{ route('expenses.index') }}" class=" nav-link {{ request()->is('expenses*') ? 'active' : '' }}btn btn-light w-100 py-4 border">
                                 <i class="bi bi-cash-stack  fs-2 d-block mb-2 text-success"></i>
                                المصروفات
                            </a>
                        </div>

                        <div class="col-6">
                            <a href="{{ route('settings.index') }}" class="btn btn-light w-100 py-4 border">
                                <i class="bi bi-gear fs-2 d-block mb-2 text-secondary"></i>
                                الإعدادات
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   @endsection
{{-- مكتبة الشارتات --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('paymentChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['مكتمل', 'جزئي', 'لم يسدد'],
                datasets: [{
                    data: [
                        {{ $paymentStatus['full'] }}, 
                        {{ $paymentStatus['partial'] }}, 
                        {{ $paymentStatus['none'] }}
                    ],
                    backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
