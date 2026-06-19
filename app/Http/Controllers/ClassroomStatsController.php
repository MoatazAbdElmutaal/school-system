<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassroomStatsController extends Controller
{
    // دالة عرض الفصول المبسطة
public function index()
{
    // نفترض أن العلاقة مع الطلاب اسمها students
    //   $teachers = Teacher::with('classroom')->get();
    // $classes = Classroom::withCount('students')->get(); 
    $classes = Classroom::with(['teacher', 'students'])->withCount('students')->get();
    return view('classrooms.index', compact('classes'));
    // في الكنترولر
}
    public function stats()
    {
        // جلب الفصول مع حساب الإحصائيات لكل فصل
        $classrooms = Classroom::withCount('students')
            ->get()
            ->map(function ($classroom) {
                // حساب إجمالي المحصل لهذا الفصل من خلال طلابه
                $totalPaid = DB::table('payments')
                    ->whereIn('student_id', function ($query) use ($classroom) {
                        $query->select('id')->from('students')->where('classroom_id', $classroom->id);
                    })->sum('amount_paid');

                $expectedTotal = $classroom->students_count * $classroom->annual_fees;
                
                // حساب النسبة المئوية
                $percentage = $expectedTotal > 0 ? round(($totalPaid / $expectedTotal) * 100, 1) : 0;

                return (object) [
                    'id' => $classroom->id,
                    'name' => $classroom->class_name,
                    'students_count' => $classroom->students_count,
                    'annual_fees' => $classroom->annual_fees,
                    'expected_total' => $expectedTotal,
                    'total_paid' => $totalPaid,
                    'remaining' => $expectedTotal - $totalPaid,
                    'percentage' => $percentage
                ];
            });

        return view('classrooms.stats', compact('classrooms'));
    }


 public function create()
{
   $teachers = Teacher::with('classroom')->get();
    return view('classrooms.create', compact('teachers') );
}

public function store(Request $request)
{
    $request->validate([
        'class_name' => 'required|string|max:255',
        'annual_fees' => 'required|numeric|min:0',
    ]);

    \App\Models\Classroom::create([
        'class_name' => $request->class_name,
        'annual_fees' => $request->annual_fees,
    ]);

    return redirect()->route('classrooms.index')->with('success', 'تم إضافة الصف بنجاح');
}
// دالة تحديث الرسوم
public function updateFee(Request $request, $id)
{
    $request->validate(['annual_fees' => 'required|numeric']);
    $class = Classroom::findOrFail($id);
    $class->update(['annual_fees' => $request->annual_fees]);
    
    return back()->with('success', 'تم تحديث الرسوم بنجاح');
}

// دالة الحذف الآمن
public function destroy($id)
{
    $class = Classroom::findOrFail($id);
    
    // منع حذف فصل به طلاب
    if($class->students()->count() > 0) {
        return back()->with('error', 'لا يمكن حذف فصل يحتوي على طلاب! قم بنقلهم أولاً.');
    }
    
    $class->delete();
    return back()->with('success', 'تم حذف الفصل بنجاح');
}

}
?>
