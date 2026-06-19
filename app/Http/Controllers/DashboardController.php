<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Expense;
use Carbon\Carbon;

class DashboardController extends Controller
{
    
    public function index() 
{
    // حسابات المعلمين (التي اتفقنا عليها)
    $teachersCount = \App\Models\Teacher::count();
    $monthlySalaryRequirement = \App\Models\Teacher::sum('salary');
    $paidSalariesThisMonth = \App\Models\Expense::where('category', 'رواتب')
        ->whereMonth('expense_date', now()->month)
        ->whereYear('expense_date', now()->year)
        ->sum('amount');
    $remainingSalaries = $monthlySalaryRequirement - $paidSalariesThisMonth;

    // 1. إحصائيات سريعة
    $stats = [
        'total_students' => \App\Models\Student::count(),
        'total_classes'  => \App\Models\Classroom::count(),
        'total_collected'=> \App\Models\Payment::sum('amount_paid'),
        // حساب إجمالي الرسوم المطلوبة من كل الطلاب
        'expected_total' => \App\Models\Student::join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
                            ->sum('classrooms.annual_fees'),
    ];

   // سداد مكتمل
    $fullCount = \App\Models\Student::whereHas('classroom', function ($q) {
        $q->whereRaw('students.id IN (SELECT student_id FROM payments GROUP BY student_id HAVING SUM(amount_paid) >= classrooms.annual_fees)');
    })->count();

    // سداد جزئي
    $partialCount = \App\Models\Student::whereHas('payments')
        ->whereHas('classroom', function ($q) {
            $q->whereRaw('students.id IN (SELECT student_id FROM payments GROUP BY student_id HAVING SUM(amount_paid) < classrooms.annual_fees)');
        })->count();

    // لم يسددوا
    $noneCount = \App\Models\Student::doesntHave('payments')->count();

    $paymentStatus = [
        'full'    => $fullCount,
        'partial' => $partialCount,
        'none'    => $noneCount,
    ];

    $totalPayments = \App\Models\Payment::sum('amount_paid'); // إجمالي الإيرادات
    $totalExpenses = \App\Models\Expense::sum('amount');    // إجمالي المصروفات
    $netProfit = $totalPayments - $totalExpenses;          // الصافي
    // عدد المعلمين الذين صرفوا هذا الشهر (بدون تكرار)
$paidTeachersCount = \App\Models\Expense::where('category', 'رواتب')
    ->whereMonth('expense_date', now()->month)
    ->whereYear('expense_date', now()->year)
    ->distinct('title') // أو أي حقل يحدد المعلم في جدول المصاريف
    ->count();

// إجمالي المعلمين (عندك أصلاً)
// $teachersCount = Teacher::count();
  return view('dashboard', compact(
        'teachersCount', 
        'monthlySalaryRequirement', 
        'paidSalariesThisMonth', 
        'remainingSalaries',
      'stats', 'paymentStatus',
      'totalPayments', 'totalExpenses', 
      'netProfit','paidTeachersCount'));
}
}
