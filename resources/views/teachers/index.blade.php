@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">👨‍🏫 إدارة المعلمين</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTeacherModal">+ إضافة معلم جديد</button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">اسم المعلم</th>
                        <th>التخصص</th>
                        <th>الفصول المسوؤل عنها </th>
                        <th>رقم الهاتف</th>
                        <th class="text-center">العمليات</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- الحلقة الأولى: مخصصة فقط لصفوف الجدول (لا نضع المودال هنا) --}}
                    @foreach($teachers as $teacher)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $teacher->name }}</td>
                        <td>{{ $teacher->subject ?? 'غير محدد' }}</td>
                        <td>
                            @if($teacher->classroom->count() > 0)
                            @foreach($teacher->classroom as $class)
                            <span class="badge bg-info text-dark">{{ $class->class_name }}</span>
                            @endforeach
                            @else
                            <span class="text-muted small">لا يوجد فصل</span>
                            @endif
                        </td>
                        <td>{{ $teacher->phone ?? '-' }}</td>
                        {{-- <td class="text-success fw-bold">{{ number_format($teacher->salary) }} ج.س</td> --}}
                        <td class="text-center">
                            {{-- زر فتح مودال التعديل --}}
                            <button class="btn btn-sm btn-outline-primary me-1" 
                                     title="تعديل بيانات المعلم"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editTeacherModal{{ $teacher->id }}">
                                <i class="bi bi-pencil"></i>
                                <span>تعديل</span>
                            </button>

                            {{-- زر الحذف --}}
                            <button class="btn btn-sm btn-outline-danger"  title="حذف المعلم"
                                    onclick="confirmDeleteTeacher({{ $teacher->id }})">
                                <i class="bi bi-trash"></i>
                                <span>حذف</span>
                            </button>

                            {{-- فورم الحذف المخفي --}}
                            <form id="delete-form-{{ $teacher->id }}" 
                                action="{{ route('teachers.destroy', $teacher->id) }}" 
                                method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div> {{-- نهاية حاوية الصفحة --}}

{{-- الحلقة الثانية: مخصصة لإنشاء مودال تعديل لكل معلم (في مكان آمن لا يكسر الـ HTML) --}}
@foreach($teachers as $teacher)
<div class="modal fade" id="editTeacherModal{{ $teacher->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('teachers.update', $teacher->id) }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">تعديل بيانات: {{ $teacher->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">الاسم الكامل</label>
                    <input type="text" name="name" value="{{ $teacher->name }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">التخصص / المادة</label>
                    <input type="text" name="subject" value="{{ $teacher->subject }}" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الراتب</label>
                        <input type="number" name="salary" value="{{ $teacher->salary }}" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الهاتف</label>
                        <input type="text" name="phone" value="{{ $teacher->phone }}" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
    <label class="form-label fw-bold d-block mb-2">الفصول المسؤولة عنها:</label>
    <div class="p-3 border rounded bg-light" style="max-height: 200px; overflow-y: auto;">
        <div class="row">
            @foreach($allClassrooms as $classroom)
                <div class="col-md-6 mb-2">
                    <div class="form-check card-checkbox">
                        <input class="form-check-input" type="checkbox" 
                               name="classrooms[]" 
                               value="{{ $classroom->id }}" 
                               id="class_{{ $teacher->id }}_{{ $classroom->id }}"
                               {{ $teacher->classroom->contains('id', $classroom->id) ? 'checked' : '' }}>
                        <label class="form-check-label ms-1" for="class_{{ $teacher->id }}_{{ $classroom->id }}">
                            {{ $classroom->class_name }}
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <small class="text-muted"><i class="bi bi-info-circle me-1"></i> يمكنك اختيار فصل واحد أو أكثر.</small>
</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary px-4">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- موديل اضافة معلم --}}
<div class="modal fade" id="addTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('teachers.store') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white">إضافة معلم جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">الاسم الكامل</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">التخصص / المادة</label>
                    <input type="text" name="subject" class="form-control">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الراتب</label>
                        <input type="number" name="salary" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">الهاتف</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary px-4">حفظ البيانات</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDeleteTeacher(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف بيانات المعلم نهائياً!",
            icon: 'warning', // يفضل warning للحذف بدلاً من error
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'تراجع',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // لتشغيل رسالة النجاح بعد إعادة تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            Swal.fire({
                toast: true,
                position: 'top-start',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000
            });
        @endif
    });
</script>

@endsection