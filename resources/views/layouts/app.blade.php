<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة الطلاب</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>

        body 
        { 
            background-color: #f8f9fa; 
            font-family: 'Cairo', sans-serif;
        
         }
        .sidebar { min-height: 100vh; background: #212529; color: white; transition: all 0.3s; }
        .sidebar .nav-link { color: #adb5bd; padding: 15px 20px; border-radius: 0; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: #343a40; color: white; }
        .main-content { padding: 20px; }
             .sidebar {
    position: sticky;
    top: 0;
    height: 100vh; /* ليأخذ طول الشاشة بالكامل */
    overflow-y: auto; /* إذا كانت عناصر القائمة كثيرة، يظهر سكرول داخلي لها فقط */
}
    </style>
</head>
<body class="{{ $bodyClass ?? '' }}">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 sidebar d-none d-md-block shadow">
            <div class="p-3 text-center border-bottom border-secondary mb-3">
                <h5 class="fw-bold mb-0 text-white">نظام المدرسة المالي</h5>
            </div>
            <ul class="nav flex-column">

                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> الرئيسية
                    </a>
                </li>
                @php 
                // نحدد إذا كان المسار الحالي يخص الطلاب لإبقاء القائمة مفتوحة
                $isStudentPath = request()->routeIs('students.*'); 
                @endphp

                <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ $isStudentPath ? '' : 'collapsed' }}" 
                data-bs-toggle="collapse" 
                href="#studentsMenu" 
                role="button" 
                aria-expanded="{{ $isStudentPath ? 'true' : 'false' }}">
                <i class="bi bi-people me-2"></i>
                <span class="mx-1">إدارة الطلاب</span>
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
                </a>

                <div class="collapse {{ $isStudentPath ? 'show' : '' }}" id="studentsMenu">
                <ul class="nav flex-column ms-1 mt-1">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('students.index') ? 'active text-primary fw-bold' : '' }}" 
                            href="{{ route('students.index') }}">
                            • عرض الطلاب
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{  request()->routeIs('students.create') ? 'active text-primary fw-bold' : '' }}" 
                            href="{{route('students.create') }}">
                            • إضافة طالب
                        </a>
                    </li>
                </ul>
                </div>
                </li>
                @php 
                // نحدد إذا كان المسار الحالي يخص الطلاب لإبقاء القائمة مفتوحة
                $isClassroomPath = request()->routeIs('classrooms.*'); 
                @endphp

                <li class="nav-item">
                <a class="nav-link d-flex align-items-center {{ $isClassroomPath ? '' : 'collapsed' }}" 
                data-bs-toggle="collapse" 
                href="#classroomsMenu" 
                role="button" 
                aria-expanded="{{ $isClassroomPath ? 'true' : 'false' }}">
                <i class="bi bi-people me-2"></i>
                <span class="mx-1">ادارة الفصول</span>
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
                </a>

                <div class="collapse {{ $isClassroomPath ? 'show' : '' }}" id="classroomsMenu">
                <ul class="nav flex-column ms-1 mt-1">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('classrooms.index') ? 'active text-primary fw-bold' : '' }}" 
                        href="{{ route('classrooms.index') }}">
                        • عرض الفصول
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('classrooms.stats') ? 'active text-primary fw-bold' : '' }}" 
                            href="{{ route('classrooms.stats') }}">
                            • احصائيات الفصول
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('classrooms.create') ? 'active text-primary fw-bold' : '' }}" 
                            href="{{route('classrooms.create') }}">
                            • إضافة فصل
                        </a>
                    </li>
                </ul>
                </div>
                </li>

                <li class="nav-item">
                   <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.index') ? 'active' : '' }}">
                     <i class="bi bi-building me-2"></i> إدارة المصروفات
                   </a>
                </li>
                <li class="nav-item">
                   <a href="{{ route('teachers.index') }}" class="nav-link  {{  request()->routeIs('teachers.index') ? 'active' : ''}}">
                     <i class="bi bi-building me-2"></i> إدارة المعلمين
                   </a>
                </li>
                <li class="nav-item">
                   <a href="{{ route('teachers.salaries') }}" class="nav-link  {{  request()->routeIs('teachers.salaries') ? 'active' : ''}}">
                     <i class="bi bi-building me-2"></i> رواتب المعلمين
                   </a>
                </li>

                <li class="nav-item">
                   <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                     <i class="bi bi-building me-2"></i> الإعدادات
                   </a>
                </li>
            </ul>
        </div>

        <div class="col-md-10 main-content">
            @yield('content') {{-- هنا سيتم حقن كود كل صفحة --}}
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts') {{-- ده المكان اللي هيتحقن فيه كود السويت أليرت --}}
</body>
</html>