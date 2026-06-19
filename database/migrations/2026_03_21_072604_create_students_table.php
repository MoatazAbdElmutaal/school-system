<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('registration_number')->unique(); 
            $table->string('national_id')->unique(); // الرقم الوطني (إضافة جديدة)
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('student_phone')->nullable(); // هاتف الطالب (nullable تعني اختياري)
            $table->string('guardian_name');
            $table->string('guardian_phone');
            $table->text('address');
            $table->foreignId('classroom_id')->constrained('classrooms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
