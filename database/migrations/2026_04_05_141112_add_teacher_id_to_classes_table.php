<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   

    /**
     * Reverse the migrations.
     */
   public function up()
{
    Schema::table('classrooms', function (Blueprint $table) {
        // إضافة حقل معرف المعلم وربطه كـ Foreign Key
        $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('classrooms', function (Blueprint $table) {
        $table->dropForeign(['teacher_id']);
        $table->dropColumn('teacher_id');
    });
}
};
