<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;
    protected $fillable = ['class_name', 'annual_fees'];

    public function students()
{
    // الفصل الواحد لديه "كثير" من الطلاب
    return $this->hasMany(Student::class);
}

public function teacher()
{
    return $this->belongsTo(Teacher::class, 'teacher_id');
}
}
