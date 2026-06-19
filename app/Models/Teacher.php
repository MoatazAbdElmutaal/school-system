<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'subject', 'salary'];
    public function classroom()
{
    return $this->hasMany(Classroom::class);
}

public function expenses()
{
    // المعلم الواحد لديه العديد من المصاريف (الرواتب)
    return $this->hasMany(\App\Models\Expense::class, 'teacher_id');
}
public function salaryPayments()
{
    // المعلم لديه دفعات رواتب كثيرة في الجدول الجديد
    return $this->hasMany(\App\Models\SalaryPayment::class, 'teacher_id');
}
}
