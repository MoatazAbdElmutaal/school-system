<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;
    // هذه المصفوفة تسمح بحفظ البيانات في هذه الأعمدة
    protected $fillable = [
        'teacher_id', 
        'amount', 
        'month', 
        'year', 
        'paid_at', 
        'notes'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
