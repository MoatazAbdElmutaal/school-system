@extends('layouts.app') {{-- تأكد إن اسم الملف هو app أو master حسب مشروعك --}}

@section('content')
<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">إدارة المصروفات 💰</h2>
            <p class="text-muted">تسجيل ومتابعة كافة تكاليف المدرسة</p>
        </div>
        <button type="button" class="btn btn-primary px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="bi bi-plus-circle me-1"></i> إضافة مصروف جديد
        </button>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">إجمالي المنصرفات</h6>
                            <h2 class="fw-bold mb-0">{{ number_format($totalExpenses) }} <small class="fs-6">ج.س</small></h2>
                        </div>
                        <i class="bi bi-cart-dash fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">التاريخ</th>
                            <th>العنوان</th>
                            <th>التصنيف</th>
                            <th>المبلغ</th>
                            <th class="text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td class="ps-4">{{ $expense->expense_date }}</td>
                            <td class="fw-bold text-dark">{{ $expense->title }}</td>
                            <td>
                                <span class="badge rounded-pill bg-info text-dark px-3">
                                    {{ $expense->category }}
                                </span>
                            </td>

                            <td class="fw-bold text-danger">{{ number_format($expense->amount) }} ج.س</td>

                            <td class="text-center">
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editExpense{{ $expense->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <div class="modal fade" id="editExpense{{ $expense->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header bg-warning">
                                    <h5 class="modal-title">تعديل مصروف: {{ $expense->title }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                    <div class="mb-3">
                                    <label class="form-label">تفاصيل المصروف</label>
                                    <input type="text" name="title" class="form-control" value="{{ $expense->title }}" required>
                                    </div>
                                    <div class="mb-3">
                                    <label class="form-label">المبلغ</label>
                                    <input type="number" name="amount" class="form-control" value="{{ $expense->amount }}" required>
                                    </div>
                                    {{-- كمل باقي الحقول (التصنيف والتاريخ) بنفس الطريقة --}}
                                    </div>
                                    <div class="modal-footer">
                                    <button type="submit" class="btn btn-warning">تحديث البيانات</button>
                                    </div>
                                    </form>
                                    </div>
                                    </div>
                                    </div>
                                {{-- <button class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button> --}}
                              <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-light border text-danger delete-btn">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">لا توجد مصروفات مسجلة حتى الآن</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">تسجيل مصروف جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">عنوان المصروف</label>
                        <input type="text" name="title" class="form-control form-control-lg" placeholder="مثلاً: فاتورة كهرباء مارس" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">المبلغ</label>
                            <input type="number" name="amount" class="form-control form-control-lg" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">التصنيف</label>
                            <select name="category" class="form-select form-select-lg" required>
                                <option value="مرتبات">مرتبات</option>
                                <option value="إيجار">إيجار</option>
                                <option value="خدمات">خدمات (كهرباء/مياه)</option>
                                <option value="صيانة">صيانة</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تاريخ الصرف</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-4">حفظ المصروف</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}"
        });
    @endif

   // عند الضغط على أي زرار حذف
    $('.delete-btn').on('click', function (e) {
        e.preventDefault(); // منع الحذف الفوري
        
        let form = $(this).closest('.delete-form'); // إمساك الفورم الخاصة بهذا السطر

        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تتمكن من التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، احذف الآن!',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // تنفيذ الحذف لو وافق المستخدم
            }
        });
    });
</script>
@endsection