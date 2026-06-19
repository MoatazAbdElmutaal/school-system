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
    Schema::table('expenses', function (Blueprint $table) { // تم تغيير Schema هنا
        $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('expenses', function (Blueprint $table) { // تم تغيير Schema هنا
        $table->dropForeign(['teacher_id']);
        $table->dropColumn('teacher_id');
    });
}
};
