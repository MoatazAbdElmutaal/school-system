<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Teacher;
use App\Models\Expense; // تأكد من استدعاء موديل المصاريف
use App\Models\SalaryPayment; // موديل سجل الرواتب
use App\Models\Classroom; // ستحتاجه أيضاً لجلب الفصول
use Illuminate\Http\Request;

class TeacherController extends Controller
{
  public function index() {
    $teachers = Teacher::with('classroom')->get(); // جلب المعلمين مع فصولهم
    
    // السطر السحري الناقص: جلب كل الفصول عشان تظهر في القائمة
    $allClassrooms = Classroom::all(); 

    return view('teachers.index', compact('teachers', 'allClassrooms'));
}

public function store(Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable',
        'subject' => 'nullable',
        'salary' => 'required|numeric',
    ]);
    Teacher::create($data);
    return back()->with('success', 'تم إضافة المعلم بنجاح');
}

public function update(Request $request, $id)
{
    $teacher = Teacher::findOrFail($id);
    
    // تحديث بيانات المعلم الأساسية
    $teacher->update($request->only(['name', 'phone', 'salary', 'specialization']));

    // 1. فك ارتباط المعلم بكل فصوله القديمة
    Classroom::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);

    // 2. ربطه بالفصول الجديدة المحددة في الـ Checkboxes
    if ($request->has('classrooms')) {
        Classroom::whereIn('id', $request->classrooms)
            ->update(['teacher_id' => $teacher->id]);
    }

    return back()->with('success', 'تم تحديث بيانات المعلم وتعيين الفصول بنجاح');
}

public function destroy($id) {
    Teacher::findOrFail($id)->delete();
    return back()->with('success', 'تم حذف المعلم بنجاح');
}
public function bulkPay(Request $request)
{
    if (!$request->has('teacher_ids')) {
        return back()->with('error', 'يرجى تحديد معلم واحد على الأقل.');
    }

    try {
        DB::transaction(function () use ($request) {
            $ids = $request->teacher_ids;
            $currentMonth = $request->selected_month;
            $currentYear = now()->year;

            foreach ($ids as $id) {
                $teacher = Teacher::findOrFail($id);
                $shortName = implode(' ', array_slice(explode(' ', $teacher->name), 0, 2));

                // 1. تسجيل في جدول SalaryPayment (مطابق للسينجل)
            $payment = SalaryPayment::create([
                    'teacher_id' => $teacher->id,
                    'amount'     => $teacher->salary,
                    'month'      => $currentMonth,
                    'year'       => $currentYear,
                    'paid_at'    => now(),
                    'notes'      => "راتب جماعي - شهر $currentMonth",
                ]);

                // 2. تسجيل في جدول Expense (تم تعديل الحقول لتطابق الكود الشغال عندك)
                Expense::create([
                    'amount'       => $teacher->salary,
                    'title'        => "راتب: $shortName (شهر $currentMonth)",
                    'category'     => 'رواتب',
                    'notes'        => "صرف راتب جماعي للمعلم: {$teacher->name} - شهر: $currentMonth",
                    'expense_date' => now(),
                    'salary_payment_id' => $payment->id, // هنا الربط حصل!
                ]);
            }
        });

        return back()->with('success', 'تمت عملية الصرف الجماعي بنجاح وتسجيلها في المصاريف.');

    } catch (\Exception $e) {
        // الخطأ هنا سيظهر لك في رسالة لو في حقل ناقص
        return back()->with('error', 'حدث خطأ أثناء الصرف: ' . $e->getMessage());
    }
}

public function paySalary(Request $request, $id)
{
    // 1. البحث عن المعلم
    $teacher = Teacher::findOrFail($id);
    $shortName = implode(' ', array_slice(explode(' ', $teacher->name), 0, 2));

    // 2. تسجيل العملية في جدول الرواتب الجديد (للحسابات الدقيقة)
    $payment = SalaryPayment::create([
        'teacher_id' => $teacher->id,
        'amount'     => $request->amount,
        'month'      => $request->month, // مثلاً "4"
        'year'       => now()->year,
        'paid_at'    => now(),
        'notes'      => "راتب شهر {$request->month}",
    ]);

    // 3. تسجيل نفس العملية في المصاريف (للميزانية العامة)
    Expense::create([
        'amount'       => $request->amount,
        'title'        => "راتب: $shortName (شهر $request->month)",
        'category'     => 'رواتب',
        'notes'        => "صرف راتب المعلم: {$teacher->name} - شهر: {$request->month}",
        'expense_date' => now(),
        'salary_payment_id' => $payment->id, // هنا الربط حصل!
    ]);

    return back()->with('success', "تم صرف راتب {$teacher->name} بنجاح");
}


public function getSalaryHistory($id)
{
    $payments = SalaryPayment::where('teacher_id', $id)
    ->orderBy('paid_at', 'desc')
                ->get();

    return response()->json($payments);
}


    public function salaries(Request $request)
    {
        // 1. استقبال الشهر من الطلب أو استخدام الشهر الحالي
        $selectedMonth = $request->query('month', now()->month);
        $currentYear = now()->year;
    
        // 2. تحديث استعلام المدرسين بناءً على الشهر المختار
        $teachers = Teacher::withCount(['salaryPayments as paid_this_month' => function ($query) use ($selectedMonth, $currentYear) {
            $query->where('month', $selectedMonth)
                  ->where('year', $currentYear);
        }])
        ->withSum(['salaryPayments as total_paid_this_month' => function ($query) use ($selectedMonth, $currentYear) {
            $query->where('month', $selectedMonth)
                  ->where('year', $currentYear);
        }], 'amount')
        ->paginate(10, ['*'], 'teachers_page'); // أضفنا البايجنيشن
    
    
    
        return view('teachers.salaries', compact('teachers', 'selectedMonth'));
    }

}
