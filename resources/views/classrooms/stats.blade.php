@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <h2 class="fw-bold mb-4">🏫 إحصائيات التحصيل حسب الفصول</h2>
<a href="{{ route('classrooms.create') }}" class="btn btn-primary my-2 shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> إضافة صف جديد
    </a>
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 px-4">اسم الصف</th>
                            <th>عدد الطلاب</th>
                            <th>رسوم الطالب</th>
                            <th>الإجمالي المطلوب</th>
                            <th>المحصل</th>
                            <th>المتبقي</th>
                            <th width="200">نسبة التحصيل</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classrooms as $class)
                        <tr>
                            <td class="px-4 fw-bold text-primary">{{ $class->name }}</td>
                            <td><span class="badge bg-secondary" style="min-width: 70px;">{{ $class->students_count }} طالب</span></td>
                            <td>{{ number_format($class->annual_fees) }}</td>
                            <td class="fw-bold">{{ number_format($class->expected_total) }}</td>
                            <td class="text-success fw-bold">{{ number_format($class->total_paid) }}</td>
                            <td class="text-danger">{{ number_format($class->remaining) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $class->percentage }}%"></div>
                                    </div>
                                    <span class="ms-2 small fw-bold">{{ $class->percentage }}%</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('students.index', ['classroom_id' => $class->id]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> الطلاب
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection