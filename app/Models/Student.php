<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
    'registration_number', 
    'national_id', 
    'full_name', 
    'date_of_birth', 
    'gender', 
    'student_phone', 
    'guardian_name', 
    'guardian_phone', 
    'address', 
    'classroom_id'
];
public function classroom()
{
    return $this->belongsTo(Classroom::class);
}
public function payments()
{
    return $this->hasMany(Payment::class);
}

// دالة لحساب إجمالي المدفوعات "النشطة" فقط
public function totalPaid()
{
    return $this->payments()->where('is_active', 1)->sum('amount_paid');
}

}


