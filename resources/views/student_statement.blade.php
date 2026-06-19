<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <title>كشف حساب: {{ $student->full_name }}</title>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f8f9fa; }
        .statement-header { background: #fff; border-bottom: 3px solid #1a5928; padding: 20px; margin-bottom: 30px; }
        .table shadow-sm { background: white; border-radius: 10px; overflow: hidden; }
        .summary-box { border-radius: 10px; padding: 15px; color: white; margin-bottom: 20px; }
        
        /* تنسيق الطباعة */
        @media print {
            .no-print { display: none; }
            body { background-color: white; }
            .card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="statement-header shadow-sm d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">كشف حساب مالي</h2>
            <p class="text-muted mb-0">اسم الطالب: <strong>{{ $student->full_name }}</strong></p>
            <p class="text-muted mb-0">الصف الدراسي: {{ $student->classroom->class_name }}</p>
        </div>
        <div class="text-end">
            <h5 class="text-primary mb-1">{{ date('d/m/Y') }}</h5>
            <p class="mb-0">رقم التسجيل: {{ $student->registration_number }}</p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="summary-box bg-primary shadow-sm">
                <h6>إجمالي الرسوم السنوية</h6>
                <h4>{{ number_format($student->classroom->annual_fees) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box bg-success shadow-sm">
                <h6>إجمالي المدفوع</h6>
                <h4>{{ number_format($totalPaid) }}</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-box bg-danger shadow-sm">
                <h6>المبلغ المتبقي</h6>
                <h4>{{number_format($remaining)  }}</h4>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white fw-bold">سجل الدفعات التفصيلي</div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>رقم الإيصال</th>
                        <th>تاريخ الدفع</th>
                        <th>المبلغ المدفوع</th>
                        <th>ملاحظات</th>
                        <th>الحالة</th>
                        <th>اجراءات</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($student->payments->sortByDesc('payment_date') as $payment)
                    <tr style="{{ $payment->is_active ? '' : 'text-decoration: line-through; color: gray; background-color: #f8f9fa;' }}">
                        <td><span class="badge bg-light text-dark border">{{ $payment->receipt_number }}</span></td>
                        <td>{{ $payment->payment_date }}</td>
                        <td class="fw-bold text-success">{{ number_format($payment->amount_paid) }}</td>
                        <td>{{ $payment->notes ?: '-' }}</td>
                        <td>
                        @if($payment->is_active)
                            <span class="badge bg-success">نشطة</span>
                        @else
                            <span class="badge bg-secondary">ملغاة</span>
                        @endif
                        </td>
                      <td>
                        <form id="toggle-form-{{ $payment->id }}" action="{{ route('payments.void', $payment->id) }}" method="POST" style="display: none;">
                            @csrf
                            @method('PATCH')
                        </form>

                        <button type="button" 
                                onclick="togglePaymentStatus({{ $payment->id }}, '{{ number_format($payment->amount_paid) }}', {{ $payment->is_active }})" 
                                class="btn btn-sm {{ $payment->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                title="{{ $payment->is_active ? 'إلغاء' : 'استعادة' }}">
                            
                            @if($payment->is_active)
                                <i class="fas fa-ban"></i> إلغاء
                            @else
                                <i class="fas fa-undo"></i> استعادة
                            @endif
                        </button>
                    </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">لا توجد دفعات مسجلة لهذا الطالب حتى الآن.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center no-print mb-5">
        <button onclick="window.print()" class="btn btn-dark btn-lg px-5 shadow">
            <i class="bi bi-printer"></i> طباعة كشف الحساب
        </button>
        <a href="/pay-fees/{{ $student->id }}" class="btn btn-outline-primary btn-lg px-4 ms-2">العودة لصفحة الدفع</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

function togglePaymentStatus(id, identifier, isActive) {
    // تحديد النصوص بناءً على الحالة
    const title = isActive ? 'هل أنت متأكد من إلغاء العملية؟' : 'هل تريد استعادة العملية؟';
    const text = isActive ? "سيتم استبعاد مبلغ (" + identifier + ") من حساب الطالب." : "سيتم إعادة احتساب مبلغ (" + identifier + ") في حساب الطالب.";
    const icon = isActive ? 'warning' : 'question';
    const confirmText = isActive ? 'نعم، إلغاء' : 'نعم، استعادة';
    const confirmColor = isActive ? '#d33' : '#28a745';

    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmColor,
        cancelButtonColor: '#3085d6',
        confirmButtonText: confirmText,
        cancelButtonText: 'تراجع',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'جاري المعالجة...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            // إرسال الـ Form برمجياً (أفضل من window.location للأمان)
            document.getElementById('toggle-form-' + id).submit();
        }
    })
}
</script>
</body>
</html>