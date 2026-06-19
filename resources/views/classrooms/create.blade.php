@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i> إضافة صف دراسي جديد</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('classrooms.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم الصف</label>
                            <input type="text" name="class_name" class="form-control" placeholder="مثلاً: الصف الأول الابتدائي" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">الرسوم الدراسية السنوية</label>
                            <div class="input-group">
                                <input type="number" name="annual_fees" class="form-control" placeholder="0.00" required>
                                <span class="input-group-text">ج.س</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">المعلم المسؤول (مربي الفصل)</label>
                            <select name="teacher_id" class="form-select">
                                <option value="">-- اختر معلماً --</option>
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }} تخصص {{ Str::length($teacher->subject) > 0 ? $teacher->subject : "لا يوحد" }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">حفظ الصف الجديد</button>
                            <a href="{{ route('classrooms.stats') }}" class="btn btn-light border py-2">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection