@extends('layouts.app')
    @section('content')
  <div class="container mt-4 mb-5">
    <div class="card shadow-sm">
        <div class=" bg-primary d-flex justify-content-between p-3">
            <div class="card-header text-white ">
                <h4 class="mb-0">نموذج تسجيل طالب جديد</h4>
            </div>
           
        </div>

         <div class="card-body p-4">
              
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
                <form action="/store-student" method="POST">
                    @csrf 

                    <div class="row">
                        <h5 class="text-primary mb-3">البيانات الأكاديمية</h5>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">رقم التسجيل</label>
                            <input type="text" name="registration_number" value="{{ $suggestedRegNum }}" class="form-control bg-light" readonly>
                            <small class="text-muted">يتم توليده تلقائياً</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">الرقم الوطني</label>
                            <input type="text" name="national_id" class="form-control" placeholder="أدخل الرقم الوطني" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">الصف الدراسي</label>
                            <select name="classroom_id" class="form-select" required>
                                <option value="">اختر الصف...</option>
                                @foreach($classrooms as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <hr class="my-4">

                        <h5 class="text-primary mb-3">بيانات الطالب الشخصية</h5>
                        <div class="col-md-8 mb-3">
                            <label class="form-label">اسم الطالب الرباعي</label>
                            <input type="text" name="full_name" class="form-control" placeholder="الاسم كما هو في الشهادة" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">رقم هاتف الطالب (اختياري)</label>
                            <input type="text" name="student_phone" class="form-control" placeholder="0XXXXXXXXX">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">الجنس</label>
                            <select name="gender" class="form-select" required>
                                <option  value="male">ذكر</option>
                                <option value="female">أنثى</option>
                            </select>
                        </div>

                        <hr class="my-4">

                        <h5 class="text-primary mb-3">بيانات التواصل وولي الأمر</h5>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم ولي الأمر</label>
                            <input type="text" name="guardian_name" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم هاتف ولي الأمر</label>
                            <input type="text" name="guardian_phone" class="form-control" required>
                        </div>

                        <div class="col-12 mb-4">
                            <label class="form-label">عنوان السكن</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="المدينة، الحي، الشارع..."></textarea>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">حفظ بيانات الطالب وبدء التسجيل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
@endsection

