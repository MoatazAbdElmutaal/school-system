<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <title>تحصيل رسوم: {{ $student->full_name }}</title>
    
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
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 700px;">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5>نافذة تحصيل الرسوم - {{ $student->full_name }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4 text-center">
                    <div class="col-4">
                        <h6>إجمالي الرسوم</h6>
                        <span class="badge bg-primary fs-6">{{ number_format($student->classroom->annual_fees) }}</span>
                    </div>
                <div class="col-4">
                <h6>المدفوع سابقاً</h6>
                <a href="/student-statement/{{ $student->id }}" class="text-decoration-none">
                <span class="badge bg-secondary fs-6" style="cursor: pointer;">{{ number_format($totalPaid) }} (عرض التفاصيل)</span>
                </a>
                </div>
                    <div class="col-4">
                        <h6>المتبقي</h6>
                        <span class="badge bg-danger fs-6">{{ number_format($remaining) }}</span>
                    </div>
                </div>

                <form action="/store-payment" method="POST">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">
                    
                    <div class="mb-3">
                        <label>المبلغ المراد دفعه الآن</label>
                        <input type="number" name="amount_paid" class="form-control" max="{{ $remaining }}" required>
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
</body>
</html>