<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryPayment; // موديل سجل الرواتب
use App\Models\Expense; // موديل سجل الرواتب

class ExpenseController extends Controller
{
   public function index()
{
    $expenses = Expense::orderBy('expense_date', 'desc')->paginate(10);
    $totalExpenses = Expense::sum('amount');
    
    return view('expenses.index', compact('expenses', 'totalExpenses'));
}

public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'amount' => 'required|numeric|min:0',
        'category' => 'required',
        'expense_date' => 'required|date',
    ]);

     Expense::create($request->all());

    return back()->with('success', 'تم تسجيل المصروف بنجاح');
}
        
public function update(Request $request, $id)
{
    $expense =  Expense::findOrFail($id);
    $expense->update($request->all());

    return back()->with('success', 'تم تحديث المصروف بنجاح');
}
public function destroy($id)
{
    $expense =  Expense::findOrFail($id);
    if ($expense->salary_payment_id) {
        SalaryPayment::where('id', $expense->salary_payment_id)->delete();
    }
    $expense->delete();

    return back()->with('success', 'تم حذف المصروف بنجاح');
}
}
