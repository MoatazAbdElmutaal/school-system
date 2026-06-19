@extends('layouts.app')

@section('content')
    
    <style>
        body { 
            background-color: #f4f7f6; 
            font-family: 'Cairo', sans-serif; /* تطبيق الخط هنا */
            direction: rtl;
        }
        .card { border: none; border-radius: 15px; overflow: hidden; }
        .card-header { background: #1a5928 !important; } /* لون أخضر داكن فخم */
        .stat-box {
            padding: 15px;
            border-radius: 10px;
            background: #fff;
            border: 1px solid #eee;
        }
        label { font-weight: 600; margin-bottom: 5px; color: #444; }
        .form-control { border-radius: 8px; padding: 10px; border: 1px solid #ddd; }
        .btn-success { background-color: #28a745; border: none; padding: 12px; font-weight: 700; border-radius: 10px; }
    </style>
    <div class="container-lg" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5>نافذة تحصيل الرسوم - {{ $student->full_name }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4 text-center">
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <h6> اجمالي الرسوم</h6>
                        <span class="badge w-50 bg-primary fs-6">{{ number_format($student->classroom->annual_fees) }}</span>
                    </div>
                <div class="col-12 col-md-4 mb-3 mb-md-0">
                <h6>المدفوع سابقاً</h6>
                <a href="/student-statement/{{ $student->id }}" class="text-decoration-none w-50">
                <span class="badge bg-secondary fs-6" style="cursor: pointer;">{{ number_format($totalPaid) }} (السجل هنا)</span>
                </a>
                </div>
                    <div class="col-12 col-md-4 mb-3 mb-md-0">
                        <h6>متبقي الرسوم</h6>
                        <span class="w-50 badge bg-danger fs-6">{{ number_format($remaining) }}</span>
                    </div>
                </div>

                <form action="/store-payment" method="POST">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    
                    <div class="mb-3">
                        <label>المبلغ المراد دفعه الآن</label>
                        @if($totalPaid < $student->classroom->annual_fees)
                        <input type="number" name="amount_paid" class="form-control" required>
                        @else
                        <input type="number" name="amount_paid" class="form-control" max="{{ $remaining }}" disabled placeholder="No fee is required">
                        @endif
                    </div>
                    
                    <div class="mb-3">
                         <label>رقم الإيصال (تلقائي)</label>
                         <input type="text" name="receipt_number" value="{{ $suggestedReceipt }}" class="form-control bg-light" readonly>
                    </div>

                    <div class="mb-3">
                        <label>تاريخ الدفع</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">تأكيد عملية الدفع</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('error'))
        const Toast = Swal.mixin({
            toast: true,
        position: 'top-start', // يظهر في أعلى اليمين (أو اليسار حسب اللغة)
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    Toast.fire({
        icon: 'error',
        title: "{{ session('error') }}"
    });
    @endif
        @if(session('success'))
        const Toast = Swal.mixin({
            toast: true,
        position: 'top-start', // يظهر في أعلى اليمين (أو اليسار حسب اللغة)
        showConfirmButton: false,
        timer: 2500,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });
    
    Toast.fire({
        icon: 'success',
        title: "{{ session('success') }}"
    });
    @endif
</script>
    @endsection