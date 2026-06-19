@extends('layouts.app')

@section('content')
<div class="main-content p-4"> 
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">⚙️ أدوات النظام</h2>
        </div>
    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm pt-3">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-person-gear fs-2 text-primary"></i>
                    </div>
                    <h5 class="fw-bold">تغيير صف طالب</h5>
                    <p class="text-muted small px-3">تعديل الفصل الدراسي للطالب وتحديث الرسوم الإجمالية آلياً.</p>
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#transferStudentModal">
                        ابدأ عملية التحويل
                    </button>
                </div>
            </div>
        </div>
        
            <div class="col-md-4">
            <div class="card border-0 shadow-sm pt-3">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                        <i class="bi bi-arrow-left-right fs-2 text-success"></i>
                    </div>
                    <h5 class="fw-bold">نقل جماعي للفصل</h5>
                    <p class="text-muted small px-3">نقل جميع طلاب فصل معين إلى فصل آخر بضغطة واحدة (مثلاً عند الترفيع).</p>
                    <button class="btn btn-success w-100 text-white" data-bs-toggle="modal" data-bs-target="#massTransferModal">
                        ابدأ النقل الجماعي
                    </button>
                </div>
            </div>
        </div>
           
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-cloud-arrow-down-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="fw-bold">تصدير البيانات</h5>
                    <p class="text-muted small">قم بتحميل نسخة احتياطية كاملة من قاعدة البيانات بصيغة .sql</p>
                    <a href="{{ route('settings.downloadBackup') }}" class="btn btn-primary w-100">
                        <i class="bi bi-download me-2"></i> تحميل النسخة الآن
                    </a>
                </div>
            </div>
        </div>
        <div class="row">

    <div class="col-md-6">
        <div class="card border-danger shadow-sm">
    <div class="card-header bg-danger text-white fw-bold">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> منطقة خطر: استعادة البيانات
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <h6 class="fw-bold">تنبيه هام جداً:</h6>
            <p class="small mb-0">عملية الاستعادة ستقوم بـ <strong>حذف كافة البيانات الحالية</strong> واستبدالها بمحتويات الملف المرفوع. لا يمكن التراجع عن هذه الخطوة إلا إذا كان لديك نسخة احتياطية.</p>
        </div>

        <form  id="restoreForm" action="{{ route('settings.restore') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">1. حمل نسخة من بياناتك الحالية أولاً (للأمان):</label>
                <a href="{{ route('settings.downloadBackup') }}" class="btn btn-outline-dark btn-sm w-100 mb-3">
                    <i class="bi bi-download me-1"></i> تحميل النسخة الحالية الآن
                </a>
                
                <label class="form-label small fw-bold">2. اختر ملف الاستعادة (.sql):</label>
                <input type="file" id="backupFile" name="backup_file" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-danger w-100" onclick="confirmRestore()">
                <i class="bi bi-cloud-arrow-up-fill me-1"></i> بدء عملية الاستبدال الكامل
            </button>
        </form>
    </div>
</div>
    </div>

</div>

        </div>
</div>

<div class="modal fade" id="transferStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">نظام تحويل الطلاب</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.transfer') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">1. ابحث عن الطالب</label>
                        <select name="student_id" class="form-select select2-enable" required style="width: 100%">
                            <option value="">اختر الطالب من القائمة...</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->full_name }} (الصف الحالي: {{ $student->classroom->class_name ?? 'غير محدد' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-success">2. اختر الصف الجديد</label>
                        <select name="class_id" class="form-select" required>
                            <option value="">انتقل إلى...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->class_name }} (الرسوم: {{ number_format($class->annual_fees) }} ج.س)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-warning small border-0">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        سيقوم النظام بتحديث "إجمالي الرسوم" المطلوبة من الطالب بناءً على الصف الجديد فور الحفظ.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-4">تأكيد التحويل الآن</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- model for mass transfer --}}
<div class="modal fade" id="massTransferModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title text-white">نظام النقل الجماعي للطلاب</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.massTransfer') }}" method="POST" id="massTransferForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">1. انقل من فصل (المصدر):</label>
                        <select name="from_class_id" class="form-select" required>
                            <option value="">اختر الفصل الحالي...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->class_name }} (يحتوي على {{ $class->students_count }} طالب)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-success">2. انقل إلى فصل (الوجهة):</label>
                        <select name="to_class_id" class="form-select" required>
                            <option value="">اختر الفصل الجديد...</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-danger small border-0 py-2">
                        <i class="bi bi-exclamation-octagon-fill me-1"></i>
                        تنبيه: سيتم تغيير فصل **جميع** الطلاب الموجودين في الفصل الأول فوراً.
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                   <button type="button" class="btn btn-success px-4 text-white" id="confirmTransferBtn">
                    تأكيد النقل الآن
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    
    function confirmRestore() {
    const fileInput = document.getElementById('backupFile');
    
    // تأكد إن المستخدم اختار ملف أولاً
    if (!fileInput.value) {
        Swal.fire('تنبيه', 'الرجاء اختيار ملف أولاً', 'warning');
        return;
    }

    Swal.fire({
        title: 'هل أنت متأكد تماماً؟',
        text: "سيتم حذف كافة البيانات الحالية واستبدالها بالملف المرفوع!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، ابدأ الاستبدال',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            // إظهار رسالة جاري التحميل
            let timerInterval;
            Swal.fire({
                title: 'جاري استعادة البيانات...',
                html: 'يرجى عدم إغلاق الصفحة، قد تستغرق العملية لحظات.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // إرسال الفورم برمجياً
            document.getElementById('restoreForm').submit();
        }
    })
}

</script>
@endsection
@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2-enable').select2({
            dropdownParent: $('#transferStudentModal'), // مهم عشان يشتغل جوه المودل
            placeholder: "ابحث عن اسم الطالب...",
            allowClear: true
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('confirmTransferBtn').addEventListener('click', function() {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيتم نقل جميع الطلاب من الفصل المختار نهائياً!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754', // لون أخضر مناسب للنجاح
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'نعم، انقل الآن!',
        cancelButtonText: 'تراجع',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // إرسال الفورم برمجياً بعد التأكيد
            document.getElementById('massTransferForm').submit();
        }
    })
});
</script>
@endsection     
