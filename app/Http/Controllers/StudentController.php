<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Student; // ستحتاجه أيضاً لجلب الفصول
use App\Models\Payment; // ستحتاجه أيضاً لجلب الفصول

class StudentController extends Controller
{
    //
public function create()
{
    // لجلب الفصول لعرضها في القائمة المنسدلة
    $classrooms = \App\Models\Classroom::all();
    
    // توليد رقم تسجيل مقترح (مثلاً STU متبوعاً بـ ID القادم)
    $nextId = \App\Models\Student::max('id') + 1;
    $suggestedRegNum = "STU-" . $nextId;

    return view('add_student', compact('classrooms', 'suggestedRegNum'));
}


public function store(Request $request)
{
    // 1. التحقق من صحة البيانات (Validation)
    $validatedData = $request->validate([
        'registration_number' => 'required|unique:students',
        'national_id'         => 'required|unique:students',
        'full_name'           => 'required|string|max:255',
        'classroom_id'        => 'required|exists:classrooms,id',
        'date_of_birth'       => 'required|date',
        'gender'              => 'required|in:male,female',
        'guardian_name'       => 'required|string',
        'guardian_phone'      => 'required',
        'student_phone'      => 'nullable',
        'address'             => 'nullable',
    ]);

    // 2. الحفظ في قاعدة البيانات
    \App\Models\Student::create($validatedData);

    // 3. إعادة التوجيه مع رسالة نجاح
    return redirect('/register-student')->with('success', 'تم تسجيل الطالب بنجاح!');
}
public function index(Request $request)
{
    $query = \App\Models\Student::with('classroom')
                ->withSum('payments as total_paid', 'amount_paid');

    // 1. فلتر البحث (بشكل عام ومستقل بالأقواس)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('full_name', 'like', "%{$search}%")
              ->orWhere('guardian_phone', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%");
        });
    }

    // 2. فلتر الفصل (لو مختار فصل)
    if ($request->filled('classroom_id')) {
        $query->where('classroom_id', $request->classroom_id);
    }

    // --- حساب الإحصائيات (للكروت) ---
    $statsQuery = clone $query; 
    $totalStudents = $statsQuery->count();
    $fullPaid = (clone $statsQuery)->whereHas('classroom', function ($q) {
        $q->whereRaw('students.id IN (SELECT student_id FROM payments GROUP BY student_id HAVING SUM(amount_paid) >= classrooms.annual_fees)');
    })->count();
    $noPaid = (clone $statsQuery)->doesntHave('payments')->count();
    $partialPaid = $totalStudents - ($fullPaid + $noPaid);

    // 3. فلتر الحالة
    if ($request->status == 'full') {
        $query->whereHas('classroom', function($q) {
            $q->whereRaw('students.id IN (SELECT student_id FROM payments GROUP BY student_id HAVING SUM(amount_paid) >= classrooms.annual_fees)');
        });
    } elseif ($request->status == 'partial') {
        $query->whereHas('payments')->whereHas('classroom', function($q) {
            $q->whereRaw('students.id IN (SELECT student_id FROM payments GROUP BY student_id HAVING SUM(amount_paid) < classrooms.annual_fees)');
        });
    } elseif ($request->status == 'none') {
        $query->doesntHave('payments');
    }

    $students = $query->paginate(15)->withQueryString();
    $allClassrooms = \App\Models\Classroom::all();

    return view('students_list', compact('students', 'totalStudents', 'fullPaid', 'partialPaid', 'noPaid', 'allClassrooms'));
}

public function payFees($id)
{
    $student = \App\Models\Student::with(['classroom', 'payments'])->findOrFail($id);
     $totalPaid = $student->totalPaid(); 
    $remaining = $student->classroom->annual_fees - $totalPaid;

    // توليد رقم إيصال تلقائي بناءً على وقت الدفع والـ ID
    $nextReceiptId = \App\Models\Payment::max('id') + 1;
    $suggestedReceipt = "REC-" . date('Y') . "-" . str_pad($nextReceiptId, 4, '0', STR_PAD_LEFT);

    return view('pay_fees', compact('student', 'totalPaid', 'remaining', 'suggestedReceipt'));
}

public function storePayment(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'amount_paid' => 'required|numeric|min:1',
        'receipt_number' => 'required|unique:payments',
        'payment_date' => 'required|date',
    ]);

    \App\Models\Payment::create($request->all());

    return redirect('/students')->with('success', 'تم تسجيل عملية الدفع بنجاح!');
}

public function statement($id)
{
    $student = Student::with('payments')->findOrFail($id);
    
    // الحسابات الآن ستعتمد على الدالة المعدلة في الموديل
    $totalPaid = $student->totalPaid(); 
    $remaining = $student->classroom->annual_fees - $totalPaid;


    return view('student_statement', compact('student', 'totalPaid', 'remaining'));
}
public function voidPayment($id)
{
    $payment = Payment::findOrFail($id);
    
    // عكس الحالة الحالية
    $newStatus = $payment->is_active ? 0 : 1;
    
    $payment->update([
        'is_active' => $newStatus
    ]);

    $message = $newStatus ? 'تم استعادة الدفعة بنجاح.' : 'تم إلغاء الدفعة واستبعادها.';

    return back()->with('success', $message);
}

public function getStudentData($id) {
    $student = Student::with(['classroom', 'payments'])->findOrFail($id);
    // حساب الإجمالي والمدفوع لإرساله للمودال
    $totalFees = $student->classroom->annual_fees;
    $paid = $student->payments->sum('amount_paid');
    return response()->json([
        'student' => $student,
        'paid' => $paid,
        'total' => $totalFees
    ]);
}

public function update(Request $request)
{
    // 1. جلب بيانات الطالب الحالية
    $student = \App\Models\Student::findOrFail($request->student_id);

    // 2. تعبئة البيانات الجديدة في الموديل (بدون حفظها في القاعدة بعد)
    $student->fill([
        'full_name'      => $request->full_name,
        'national_id'    => $request->national_id,
        'guardian_phone' => $request->guardian_phone,
        'address'        => $request->address,
    ]);

    // 3. التحقق: هل تغير شيء فعلياً؟
    if (!$student->isDirty()) {
        // إذا لم يتغير شيء، نعود للخلف مع رسالة تنبيه خفيفة (Info) بدلاً من نجاح
        return back()->with('info', 'لم يتم تغيير أي بيانات.');
    }

    // 4. إذا وجد تغيير، نقوم بالحفظ
    $student->save();

    return back()->with('success', 'تم تحديث البيانات بنجاح.');
}

    public function destroy($id)
{
    // 1. البحث عن الطالب
    $student = \App\Models\Student::findOrFail($id);

    // 2. حذف الطالب (سيقوم لارافل بحذف سجلاته المرتبطة إذا كنت قد وضعت On Delete Cascade)
    $student->delete();

    // 3. العودة للخلف مع رسالة نجاح لكي يلقطها الـ SweetAlert
    return redirect()->back()->with('success', 'تم حذف الطالب وجميع بياناته بنجاح');
}
}
