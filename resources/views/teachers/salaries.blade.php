@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-wallet2 me-2"></i>إدارة رواتب المعلمين</h2>
        {{-- <span class="badge bg-dark p-2">شهر: {{ now()->translatedFormat('F Y') }}</span> --}}
        <form action="{{ route('teachers.salaries') }}" method="GET" id="filterForm" class="d-inline-block">
    <select name="month" class="form-select form-select-sm bg-dark text-white" onchange="document.getElementById('filterForm').submit()">
        @for ($i = 1; $i <= 12; $i++)
            <option value="{{ $i }}" {{ request('month', now()->month) == $i ? 'selected' : '' }}>
                {{ date("F", mktime(0, 0, 0, $i, 1)) }} {{ now()->year }}
            </option>
        @endfor
    </select>
</form>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between">
            <h5 class="mb-0 fw-bold text-primary">قائمة استحقاقات الرواتب</h5>
            <button type="button" 
        class="btn btn-primary shadow-sm fw-bold" 
        data-bs-toggle="modal" 
        data-bs-target="#bulkPayModal">
    <i class="bi bi-cash-stack me-1"></i>
    صرف رواتب الكل
</button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%">اسم المعلم</th>
                        <th>الراتب الأساسي</th>
                        <th>صرف الشهر</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($teachers as $teacher)
                     <tr>
                            <td>
                                <div class="fw-bold">{{ $teacher->name }}</div>
                                <small class="text-muted">{{ $teacher->specialization }}</small>
                            </td>
                            <td class="fw-bold text-dark">
                                {{ number_format($teacher->salary) }} ج.س
                            </td>
                            <td>
                                @if($teacher->paid_this_month > 0)
                                <span class="badge bg-success">نعم (تم الصرف)</span>
                                @else
                                <span class="badge bg-danger">لا (لم يصرف)</span>
                                @endif
                            </td>
                            <td class="text-center">
                            
                     <button type="button" 
                        class="btn btn-outline-success btn-sm btn-pay" 
                        data-bs-toggle="modal" 
                        data-bs-target="#paySalaryModal"
                        data-id="{{ $teacher->id }}"
                        data-name="{{ $teacher->name }}"
                        data-salary="{{ $teacher->salary }}">
                    دفع الراتب
                   </button>
                  <button type="button" 
        class="btn btn-outline-info btn-sm view-history" {{-- لاحظ شلنا الـ data-bs --}}
        data-id="{{ $teacher->id }}"
        data-name="{{ $teacher->name }}">
    السجل
</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- مودل دفع الراتب --}}
    <div class="modal fade" id="paySalaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="" method="POST" id="paySalaryForm" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white">صرف راتب: <span id="modalTeacherNameDisplay"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3 text-center">
                    <h6 class="text-muted">الراتب الأساسي المسجل</h6>
                    <h3 class="fw-bold text-success"><span id="modalSalaryDisplay"></span> ج.س</h3>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">عن شهر</label>
                    <select name="month" id="modalMonthSelect" class="form-select" required>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == $selectedMonth ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }} ({{ $i }})
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">المبلغ المدفوع</label>
                    <input type="number" name="amount" id="modalAmountInput" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success px-4">تأكيد عملية الصرف</button>
            </div>
        </form>
    </div>
</div>

{{-- مودل سجل المرتبات للمعلم --}}
 <div class="modal fade" id="historyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title text-white">سجل رواتب: <span id="historyTeacherName"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الشهر / السنة</th>
                                <th>المبلغ</th>
                                <th>تاريخ الصرف</th>
                            </tr>
                        </thead>
                        <tbody id="historyTableBody">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- موديل دفع كل الرواتب --}}
<div class="modal fade" id="bulkPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg border-0">
        <form action="{{ route('teachers.bulk_pay') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <input type="hidden" name="selected_month" value="{{ request('month', now()->month) }}">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i class="bi bi-people-fill me-2"></i>صرف رواتب جماعي</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0">
                <div class="d-flex justify-content-around bg-light py-3 border-bottom text-center">
                    <div>
                        <small class="text-muted d-block">عدد المعلمين</small>
                        <span class="fw-bold text-primary" id="selectedCount">0</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">إجمالي المبلغ</small>
                        <span class="fw-bold text-success" id="totalBulkAmount">0</span> <small>ج.س</small>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 50px;">
                                    <input type="checkbox" class="form-check-input" id="selectAllTeachers" checked>
                                </th>
                                <th>اسم المعلم</th>
                                <th>الراتب الأساسي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $teacher)
                            @if($teacher->paid_this_month <= 0) {{-- عرض فقط من لم يصرف له --}}
                            <tr>
                                <td>
                                    <input type="checkbox" name="teacher_ids[]" 
                                           class="form-check-input teacher-checkbox" 
                                           value="{{ $teacher->id }}" 
                                           data-salary="{{ $teacher->salary }}" 
                                           checked>
                                </td>
                                <td>{{ $teacher->name }}</td>
                                <td class="fw-bold">{{ number_format($teacher->salary) }} ج.س</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary px-4">تأكيد صرف الرواتب المحددة</button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    
    // --- أولاً: كود مودال دفع الراتب ---
    const payModal = document.getElementById('paySalaryModal');
    if (payModal) {
        payModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const salary = button.getAttribute('data-salary');

            payModal.querySelector('#modalTeacherNameDisplay').textContent = name;
            payModal.querySelector('#modalSalaryDisplay').textContent = new Intl.NumberFormat().format(salary);
            payModal.querySelector('#modalAmountInput').value = salary;
            payModal.querySelector('#paySalaryForm').action = `/teachers/pay_salary/${id}`; 
        });
    }

    // --- ثانياً: كود مودال سجل الرواتب ---
    const historyModalElem = document.getElementById('historyModal');
    const historyModalInstance = historyModalElem ? new bootstrap.Modal(historyModalElem) : null;

   document.querySelectorAll('.view-history').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const name = this.getAttribute('data-name');
        
        // الوصول المباشر للعناصر بالـ ID يحل مشكلة الـ querySelector تماماً
        const nameElem = document.getElementById('modalTeacherName'); // تأكد أن الـ ID مطابق في الـ HTML
        const tbody = document.getElementById('historyTableBody');
        
        if (nameElem) nameElem.innerText = name;
        
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center">جاري التحميل...</td></tr>';
        }

        // إظهار المودال (تأكد أن المتغير historyModalInstance معرف في بداية السكريبت)
        if(typeof historyModalInstance !== 'undefined' && historyModalInstance) {
            historyModalInstance.show();
        }

        // طلب البيانات
        fetch(`/teachers/salary-history/${id}`)
            .then(response => response.json())
            .then(data => {
                if (tbody) {
                    tbody.innerHTML = '';
                    if(data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center">لا توجد دفعات مسجلة</td></tr>';
                    } else {
                        data.forEach(pay => {
                            tbody.innerHTML += `
                                <tr>
                                    <td>${pay.month} / ${pay.year}</td>
                                    <td class="text-success fw-bold">${new Intl.NumberFormat().format(pay.amount)} ج.س</td>
                                    <td>${pay.paid_at}</td>
                                </tr>`;
                        });
                    }
                }
            });
    });
});

// كود دفع كل الرواتب
// دالة لتحديث الإجمالي والعدد
function updateBulkTotals() {
    let total = 0;
    let count = 0;
    document.querySelectorAll('.teacher-checkbox:checked').forEach(cb => {
        total += parseFloat(cb.getAttribute('data-salary'));
        count++;
    });
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('totalBulkAmount').textContent = new Intl.NumberFormat().format(total);
}

// تشغيل الحساب عند فتح المودال وعند أي تغيير في الـ Checkboxes
document.getElementById('bulkPayModal').addEventListener('shown.bs.modal', updateBulkTotals);

document.querySelectorAll('.teacher-checkbox').forEach(cb => {
    cb.addEventListener('change', updateBulkTotals);
});

// ميزة تحديد الكل / إلغاء الكل
document.getElementById('selectAllTeachers').addEventListener('change', function() {
    document.querySelectorAll('.teacher-checkbox').forEach(cb => {
        cb.checked = this.checked;
    });
    updateBulkTotals();
});
});
</script>

 @if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-start',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'success',
        title: "{{ session('success') }}",
        background: '#f8fff9',
        color: '#1a5928'
    });
</script>
@endif
 @if(session('error'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-start',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: 'error',
        title: "{{ session('error') }}",
        background: '#f8fff9',
        color: '#1a5928'
    });
</script>
@endif
@endsection