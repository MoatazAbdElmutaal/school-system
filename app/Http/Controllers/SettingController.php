<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting; // تأكد إنك عملت الموديل ده
use App\Models\Classroom; // تأكد إنك عملت الموديل ده
use App\Models\Student; // تأكد إنك عملت الموديل ده
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{

public function index()
{
    // 1. جلب الطلاب مع "فصولهم" (للبطاقة الزرقاء - نقل فردي)
    $students = Student::with('classroom')->get();

    // 2. جلب الفصول مع "عدد طلابها" (للبطاقة الخضراء - نقل جماعي)
    $classes = Classroom::withCount('students')->get();

    // 3. تمرير المتغيرات بشكل منفصل ومنظم
    return view('settings.index', compact('students', 'classes'));
}

public function transfer(Request $request)
{
    $request->validate([
        'student_id' => 'required|exists:students,id',
        'class_id' => 'required|exists:classrooms,id', // ده اسم الـ Input من الفورم
    ]);

    $student = \App\Models\Student::findOrFail($request->student_id);
    $newClass = \App\Models\Classroom::findOrFail($request->class_id);

    // التعديل هنا: اسم العمود في قاعدة البيانات هو classroom_id وليس class_id
    // واسم عمود السعر غالباً هو class_fees أو fees حسب جدولك
    $student->update([
        'classroom_id' => $newClass->id, 
        'annual'   => $newClass->class_fees // تأكد من اسم عمود الرسوم في جدول classrooms
    ]);

return back()->with('success', "تم تحويل الطالب ( " . $student->full_name . " ) إلى ( " . $newClass->class_name . " ) بنجاح");
}

public function massTransfer(Request $request)
{
    $request->validate([
        'from_class_id' => 'required',
        'to_class_id' => 'required',
    ]);

    // تحديث كل الطلاب من الفصل القديم إلى الجديد
    $affected = Student::where('classroom_id', $request->from_class_id)
                ->update(['classroom_id' => $request->to_class_id]);

    return back()->with('success', "تم نقل $affected طالب بنجاح!");
}

public function downloadBackup()
{
    // 1. تجهيز اسم الملف والمسار
    $filename = "backup-" . now()->format('Y-m-d-H-i-s') . ".sql";
    $path = storage_path('app/' . $filename);

    // 2. جلب بيانات قاعدة البيانات من الـ .env تلقائياً
    $username = config('database.connections.mysql.username');
    $password = config('database.connections.mysql.password');
    $database = config('database.connections.mysql.database');
    $host     = config('database.connections.mysql.host');

    // 3. تحديد مسار أداة mysqldump (تأكد من صحة مسار XAMPP عندك)
    $mysqldumpPath = 'C:\xampp\mysql\bin\mysqldump.exe';

    // 4. بناء الأمر (معالجة حالة وجود كلمة مرور أو عدمها)
    // نستخدم الـ quotes حول المسار لضمان عدم حدوث خطأ إذا كان هناك مسافات في مجلدات الويندوز
    $passwordPart = !empty($password) ? "-p\"$password\"" : "";
    
    $command = sprintf(
        '"%s" -u%s %s -h%s %s > "%s"',
        $mysqldumpPath,
        $username,
        $passwordPart,
        $host,
        $database,
        $path
    );

    // 5. تنفيذ الأمر
    $output = [];
    $returnVar = null;
    exec($command, $output, $returnVar);

    // 6. التحقق من النتيجة وتنزيل الملف
    if ($returnVar === 0 && file_exists($path) && filesize($path) > 0) {
        return response()->download($path)->deleteFileAfterSend(true);
    } else {
        // إذا فشل الأمر، امسح الملف الفارغ وارجع برسالة خطأ
        if (file_exists($path)) { unlink($path); }
        
        return back()->with('error', 'فشل في إنشاء نسخة احتياطية. تأكد من إعدادات السيرفر.');
    }
}

public function restore(Request $request)
{
    $request->validate([
        'backup_file' => 'required|file',
    ]);

    $file = $request->file('backup_file');
    $path = $file->getRealPath();

    // إعدادات القاعدة
    $dbConfig = config('database.connections.mysql');
    $username = $dbConfig['username'];
    $password = $dbConfig['password'];
    $database = $dbConfig['database'];
    $host     = $dbConfig['host'];

    // مسار mysql.exe (وليس mysqldump)
    $mysqlPath = 'C:\xampp\mysql\bin\mysql.exe';

    $passwordPart = !empty($password) ? "-p\"$password\"" : "";

    // أمر الحقن (Import)
    $command = sprintf(
        '"%s" -u%s %s -h%s %s < "%s"',
        $mysqlPath,
        $username,
        $passwordPart,
        $host,
        $database,
        $path
    );

    $resultCode = null;
    $output = [];
    exec($command, $output, $resultCode);

    if ($resultCode === 0) {
        return back()->with('success', 'تمت استعادة البيانات بنجاح واستبدال النسخة القديمة.');
    } else {
        return back()->with('error', 'فشل في استعادة البيانات. تأكد من صحة ملف الـ SQL.');
    }
}
}

