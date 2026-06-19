@extends('layouts.app') {{-- هنا بننادي الملف الرئيسي اللي فيه السايدبار --}}
@section('content') {{-- هنا بنقول للارافل: حط الكود الجاي ده في منطقة المحتوى --}}
<div class="d-flex justify-content-between align-items-center mb-4 ">
 <div class="dropdown shadow-sm my-2">
    <button class="btn btn-white border dropdown-toggle fw-bold text-primary px-4" type="button" id="classroomSelect" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-funnel-fill me-1"></i>
        @if(request('classroom_id'))
            {{ $allClassrooms->where('id', request('classroom_id'))->first()->class_name ?? 'تصفية حسب الصف' }}
        @else
            عرض كل طلاب المدرسة
        @endif
    </button>
    
    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="classroomSelect" style="min-width: 220px;">
        <li>
            <a class="dropdown-item py-2 {{ !request('classroom_id') ? 'active bg-primary' : '' }}" href="{{ route('students.index') }}">
                <i class="bi bi-grid-fill me-2"></i> عرض كل طلاب المدرسة
            </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        @foreach($allClassrooms as $cls)
            <li>
                <a class="dropdown-item py-2 {{ request('classroom_id') == $cls->id ? 'active' : '' }}" 
                   href="{{ route('students.index', ['classroom_id' => $cls->id]) }}">
                   <i class="bi bi-door-closed me-2"></i> {{ $cls->class_name }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
</div>
<div class="row g-4 mb-3 text-center card-filterable">
    <div class="col-md-3">
    <a href="{{ route('students.index', ['classroom_id' => request('classroom_id')]) }}" class="text-decoration-none">            <div class="card shadow-sm border-0 bg-dark text-white h-100 {{ !request('status') ? 'active-filter' : '' }}">
                <div class="card-body">
                    <i class="bi bi-people fs-2 text-white-50 float-start"></i>
                    <small class="d-block text-white">إجمالي الطلاب</small>
                    <h2 class="fw-bold mb-0">{{ $totalStudents }}</h2>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('students.index', ['status' => 'full', 'classroom_id' => request('classroom_id')]) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 bg-success text-white h-100 {{ request('status') == 'full' ? 'active-filter' : '' }}">
                <div class="card-body">
                    <i class="bi bi-check-circle fs-2 text-white float-start"></i>
                    <small class="d-block text-white">سداد مكتمل</small>
                    <h2 class="fw-bold mb-0">{{ $fullPaid }}</h2>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
               <a href="{{ route('students.index', ['status' => 'partial', 'classroom_id' => request('classroom_id')]) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 bg-warning text-dark h-100 {{ request('status') == 'partial' ? 'active-filter' : '' }}">
                <div class="card-body">
                    <i class="bi bi-hourglass-split fs-2 text-dark-50 float-start"></i>
                    <small class="d-block text-dark-50">سداد جزئي</small>
                    <h2 class="fw-bold mb-0">{{ $partialPaid }}</h2>
                </div>
            </div>
        </a>
    </div>

    <div class="col-md-3">
        <a href="{{ route('students.index', ['status' => 'none', 'classroom_id' => request('classroom_id')]) }}" class="text-decoration-none">
            <div class="card shadow-sm border-0 bg-danger text-white h-100 {{ request('status') == 'none' ? 'active-filter' : '' }}">
                <div class="card-body">
                    <i class="bi bi-exclamation-octagon fs-2 text-white float-start"></i>
                    <small class="d-block text-white">لم يسددوا</small>
                    <h2 class="fw-bold mb-0">{{ $noPaid }}</h2>
                </div>
            </div>
        </a>
    </div>
</div>  
    @if(request('search'))
    <div class="alert alert-light border-0 shadow-sm mb-3 d-flex justify-content-between align-items-center">
        <span>
            <i class="bi bi-info-circle me-2"></i>
            نتائج البحث عن: <strong>"{{ request('search') }}"</strong> 
            <small class="text-muted">({{ $students->total() }} طالب)</small>
        </span>
        <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-danger">إلغاء البحث</a>
    </div>
@endif
    
<form action="{{ route('students.index') }}" method="GET" class="mb-4">
    {{-- الحفاظ على الفلاتر المخفية --}}
    @if(request('classroom_id'))
        <input type="hidden" name="classroom_id" value="{{ request('classroom_id') }}">
    @endif
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="input-group shadow-sm">
               
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       class="form-control border-start-0 ps-0" 
                       placeholder="ابحث بالاسم، الهاتف، أو الرقم الوطني..."
                       style="box-shadow: none;">
                <button class="btn btn-primary px-4 fw-bold" type="submit">
                    بحث
                </button>
            </div>
            
        </div>
    </div>
</form>

<div class="card shadow  mb-3">
        <div class="card-header pt-3 pb-2 bg-dark text-white d-flex justify-content-between">
            <h4 class="mb-0">
            قائمة الطلاب المسجلين 
            </h4>
            <a href="/register-student" class="btn btn-primary btn-sm">تسجيل طالب جديد</a>
        </div>
        
        <div class="card-body card-table">
            <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم التسجيل</th>
                        <th>الاسم الكامل</th>
                        <th>الصف الدراسي</th>
                        <th>رقم ولي الأمر</th>
                        <th>العمليات</th>
                    </tr>
                </thead>
                <tbody id="studentTable">
                   @foreach($students as $student)
                    @php
                    // بدل ما نجمع هنا ونرهق السيرفر، بناخد القيمة الجاهزة اللي بعتها الكنترولر
                    $paid = $student->total_paid ?? 0; 
                    $fees = $student->classroom->annual_fees ?? 0;

                    // تحديد الحالة (نفس المنطق لكن ببيانات أسرع)
                    $status = 'none';
                    if($fees > 0 && $paid >= $fees) $status = 'full';
                    elseif($paid > 0) $status = 'partial';
                    @endphp
                    <tr class="student-row" data-status="{{ $status }}">
                        <td>{{ $student->registration_number }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->classroom->class_name }}</td>
                        <td>{{ $student->guardian_phone }}</td>
                       <td>
                            <div class="d-flex gap-2">
                            <a href="{{ $status == 'full' ? '/pay-fees/'.$student->id : '/pay-fees/'.$student->id }}" 
                            class="btn btn-{{ $status == 'full' ? 'secondary' : 'success' }} btn-sm"
                             style="width: 100px; height: 31px;">
                            <i class="bi bi-{{ $status == 'full' ? 'patch-check-fill' : 'cash-stack' }}"></i> 
                            {{ $status == 'full' ? 'تم السداد' : 'دفع رسوم' }}
                            </a>
                            <button type="button" onclick="showStudent({{ $student->id }})" class="btn btn-primary btn-sm">تعديل</button>
                            
                            <button type="button" onclick="quickDelete({{ $student->id }}, '{{ $student->full_name }}')" class="btn btn-danger btn-sm">حذف</button>
                             </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>      
            </div>
        </div>
        <div style="padding-right: 20px; padding-left: 20px;">
            {{ $students->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <div class="modal-content" style="font-family: 'Cairo', sans-serif;">
            <form id="editForm" action="/student-update" method="POST">
                @csrf
                <input type="hidden" name="student_id" id="m_id">
                
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>ملف الطالب الإلكتروني</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 border-start bg-light p-4">
                            <div class="mb-3">
    <label class="text-secondary small">الموقف المالي</label>
    <div id="payment_status_badge" class="badge p-2 d-block text-center fs-6">جاري التحميل...</div>
</div>
                            <div class="mb-3">
                                <label class="text-secondary small">الصف الدراسي</label>
                                <p class="fw-bold" id="view_classroom">-</p>
                            </div>
                            <div class="mb-3">
                                <label class="text-secondary small">إجمالي الرسوم السنوية</label>
                                <p class="fw-bold text-primary" id="view_fees">0</p>
                            </div>
                            <hr>
                          <div class="alert alert-info py-2">
                            <small>
                                ملاحظة: لتغيير الصف أو الرسوم، يرجى الانتقال إلى 
                                <a href="{{ route('settings.index') }}" class="fw-bold text-primary text-decoration-none">
                                    قسم الإعدادات <i class="bi bi-box-arrow-up-right small"></i>
                                </a>.
                            </small>
                        </div>
                        </div>

                        <div class="col-md-8 p-4">
                            <h6 class="text-muted mb-3">تعديل البيانات الأساسية</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label>الاسم الكامل</label>
                                    <input type="text" name="full_name" id="m_name" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>الرقم الوطني</label>
                                    <input type="text" name="national_id" id="m_national" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label>هاتف ولي الأمر</label>
                                    <input type="text" name="guardian_phone" id="m_phone" class="form-control">
                                </div>
                                <div class="col-md-12">
                                    <label>العنوان السكني</label>
                                    <textarea name="address" id="m_address" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button id="saveBtn" type="submit" class="btn btn-primary px-4">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    let originalData = {};
function showStudent(id) {
    $.get('/student-data/' + id, function(data) {
        originalData = {
            name: data.student.full_name,
            national: data.student.national_id,
            phone: data.student.guardian_phone,
            address: data.student.address
        };

        let student = data.student;
        let paid = data.paid;
        let total = data.total;

        // ملء البيانات الأساسية
        $('#m_id').val(student.id);
        $('#m_name').val(student.full_name);
        $('#m_national').val(student.national_id);
        $('#m_phone').val(student.guardian_phone);
        $('#m_address').val(student.address);
        $('#view_classroom').text(student.classroom.class_name);
        $('#view_fees').text(new Intl.NumberFormat().format(total) + ' ج.س');

        // تحديد حالة الموقف المالي بالألوان
        let statusBadge = $('#payment_status_badge');
        statusBadge.removeClass('bg-success bg-warning bg-danger');

        if (paid >= total) {
            statusBadge.text('مكتمل').addClass('bg-success');
        } else if (paid > 0) {
            statusBadge.text('دفع جزئي').addClass('bg-warning text-dark');
        } else {
            statusBadge.text('لم يتم الدفع').addClass('bg-danger');
        }

        // تعطيل زر الحفظ عند الفتح
        $('#saveBtn').prop('disabled', true);

        var myModal = new bootstrap.Modal(document.getElementById('studentModal'));
        myModal.show();
    });
}
// مراقبة أي كتابة في الحقول
$('#editForm input, #editForm textarea').on('input', function() {
    let currentData = {
        name: $('#m_name').val(),
        national: $('#m_national').val(),
        phone: $('#m_phone').val(),
        address: $('#m_address').val()
    };

    // إذا كانت البيانات الحالية تختلف عن الأصلية، فعل الزر
    let isChanged = JSON.stringify(originalData) !== JSON.stringify(currentData);
    $('#saveBtn').prop('disabled', !isChanged);
});

$('#editForm').on('submit', function(e) {
    // تعطيل الزر وتغيير النص
    $('#saveBtn').prop('disabled', true);
    $('#saveBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> جاري الحفظ...');
    // إظهار رسالة الانتظار فوراً
    // Swal.fire({
    //     title: 'جاري حفظ البيانات...',
    //     html: 'يرجى الانتظار لحظات',
    //     allowOutsideClick: false,
    //     didOpen: () => {
    //         Swal.showLoading(); // تشغيل حركة اللودر (Spinner)
    //     }
    // });
    
    // السماح للفورم بالإرسال بعد إظهار الرسالة
    return true; 
});

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

// @if(session('success'))
//     Swal.fire({
//         icon: 'success',
//         title: 'تمت العملية',
//         text: "{{ session('success') }}",
//         timer: 2500,
//         showConfirmButton: false,
//         fontFamily: 'Cairo'
//     });
// @endif

function quickDelete(id, name) {
    Swal.fire({
        title: 'هل أنت متأكد؟',
        text: "سيتم حذف الطالب (" + name + ") وجميع سجلاته المالية نهائياً!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'نعم، احذف الآن',
        cancelButtonText: 'إلغاء',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
                        Swal.fire({
                title: 'جاري الحذف...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // توجيه المستخدم للحذف
            window.location.href = "/student-delete/" + id;
        }
    })
}
$(document).ready(function() {
    const filters = {
        'filter-total': { status: 'all', color: '#6c757d' }, // رمادي غامق
        'filter-full': { status: 'full', color: '#198754' },  // أخضر
        'filter-partial': { status: 'partial', color: '#ffc107' }, // أصفر
        'filter-none': { status: 'none', color: '#dc3545' }    // أحمر
    };

//     $('#filter-total, #filter-full, #filter-partial, #filter-none').on('click', function() {
//         const filterData = filters[$(this).attr('id')];
        
//         // 1. تمييز الكرت المختار بصرياً
//         $('.card').css({ 'transform': 'scale(1)', 'box-shadow': 'none', 'opacity': '1' });
//         $(this).css({ 'transform': 'scale(1.05)', 'box-shadow': '0 10px 20px rgba(0,0,0,0.2)', 'opacity': '1' });

//         // 2. تغيير لون إطار الجدول ليتناسب مع الفلتر (لمسة فخامة)
//         $('.card-table').css('border-top', '5px solid ' + filterData.color);

//         // 3. منطق الفلترة
//         if (filterData.status === 'all') {
//             $('.student-row').fadeIn(300);
//         } else {
//             $('.student-row').hide();
//             $('.student-row[data-status="' + filterData.status + '"]').fadeIn(300);
//         }
//     });
});
</script>
@endsection