@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">🏫 إدارة وعرض الفصول</h2>
        <a href="{{ route('classrooms.create') }}" class="btn btn-primary">+ إضافة فصل جديد</a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
           <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">اسم الصف</th>
                        <th>مربي الفصل</th>
                        <th>عدد الطلاب</th>
                        <th>الرسوم السنوية</th>
                        <th class="text-center">إدارة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $class)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">{{ $class->class_name }}</td>
                        <td class="ps-4 fw-bold text-primary">{{ $class->teacher ? $class->teacher->name : 'لا يوجد معلم' }}</td>
                        <td><span class="badge bg-secondary">{{ $class->students_count }} طالب</span></td>
                        <td class="fw-bold">{{ number_format($class->annual_fees) }} ج.س</td>
                        <td class="text-center">
                            <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-info text-white me-2" data-bs-toggle="modal" data-bs-target="#editFeeModal{{ $class->id }}">
                                <i class="bi bi-pencil-square"></i> الرسوم
                            </button>

                            <form action="{{ route('classrooms.destroy', $class->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الفصل نهائياً؟')">
                                    <i class="bi bi-trash"></i> حذف
                                </button>
                            </form>
                            
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="editFeeModal{{ $class->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title">تعديل رسوم: {{ $class->class_name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('classrooms.updateFee', $class->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <label class="form-label fw-bold">الرسوم الجديدة (ج.س)</label>
                                        <input type="number" name="annual_fees" class="form-control" value="{{ $class->annual_fees }}" required>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary">حفظ التعديل</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>      
        </div>
        </div>
    </div>
</div>

@if(session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        toast: true,
        position: 'top-start',
        icon: 'success',
        title: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 3000
    });
</script>
@endif

@endsection