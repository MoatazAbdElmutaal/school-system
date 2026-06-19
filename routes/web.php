<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DashboardController;
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/register-student', [StudentController::class, 'create'])->name('students.create');
Route::post('/store-student', [StudentController::class, 'store']);
Route::get('/students', [StudentController::class, 'index'])->name('students.index');// {id} تعني أننا سنمرر رقم الطالب في الرابط
Route::get('/pay-fees/{id}', [StudentController::class, 'payFees']);
Route::post('/store-payment', [StudentController::class, 'storePayment']);
Route::get('/student-statement/{id}', [StudentController::class, 'statement']);
Route::get('/student-data/{id}', [StudentController::class, 'getStudentData']); // لجلب البيانات للمودل
Route::post('/student-update', [StudentController::class, 'update']); // لحفظ التعديل
Route::get('/student-delete/{id}', [StudentController::class, 'destroy']);

use App\Http\Controllers\ClassroomStatsController;
Route::get('/classrooms/stats', [ClassroomStatsController::class, 'stats'])->name('classrooms.stats');

Route::get('/classrooms/create', [ClassroomStatsController::class, 'create'])->name('classrooms.create');
Route::post('/classrooms/store', [ClassroomStatsController::class, 'store'])->name('classrooms.store');
Route::get('/classrooms', [ClassroomStatsController::class, 'index'])->name('classrooms.index');
Route::put('/classrooms/{id}/update-fee', [ClassroomStatsController::class, 'updateFee'])->name('classrooms.updateFee');
Route::delete('/classrooms/{id}', [ClassroomStatsController::class, 'destroy'])->name('classrooms.destroy');

use App\Http\Controllers\ExpenseController;
Route::resource('expenses', ExpenseController::class);

use App\Http\Controllers\SettingController;
Route::post('/settings/transfer', [SettingController::class, 'transfer'])->name('settings.transfer');
Route::post('/settings/massTransfer', [SettingController::class, 'massTransfer'])->name('settings.massTransfer');
Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
Route::get('/settings/backup-download', [SettingController::class, 'downloadBackup'])->name('settings.downloadBackup');
Route::post('/settings/restore', [SettingController::class, 'restore'])->name('settings.restore');

use App\Http\Controllers\TeacherController;
// مسارات إدارة المعلمين
Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
Route::post('/teachers/store', [TeacherController::class, 'store'])->name('teachers.store');
// أضف هذا المسار أيضاً للحذف إذا كنت ستستخدمه
Route::delete('/teachers/{id}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
Route::put('/teachers/{id}', [TeacherController::class, 'update'])->name('teachers.update');

Route::get('/salaries', [TeacherController::class, 'salaries'])->name('teachers.salaries');
Route::get('/teachers/salary-history/{id}', [TeacherController::class, 'getSalaryHistory']);
Route::post('/teachers/pay_salary/{id}', [TeacherController::class, 'paySalary'])->name('teachers.pay_salary');
Route::post('/teachers/bulk_pay', [TeacherController::class, 'bulkPay'])->name('teachers.bulk_pay');
Route::patch('/payments/{id}/void', [StudentController::class, 'voidPayment'])->name('payments.void');