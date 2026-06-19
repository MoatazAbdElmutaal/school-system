<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    
    // الحقول المسموح بتخزينها (التي أرسلناها من الفورم)
    protected $fillable = [
        'title',
        'amount',
        'category',
        'expense_date',
        'notes',
        'teacher_id', 
    'salary_payment_id' // <--- ل
    ];

}
