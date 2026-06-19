<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('payments', function (Blueprint $table) {
        // 1 يعني نشطة ومحتسبة، 0 يعني ملغاة وغير محتسبة
        $table->boolean('is_active')->default(1)->after('amount_paid');
    });
}

public function down()
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn('is_active');
    });
}
};
