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
    Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        $table->string('title'); // وصف المصروف (مثلاً: فاتورة كهرباء شهر 3)
        $table->decimal('amount', 15, 2); // المبلغ المنصرف
        $table->string('category'); // التصنيف (مرتبات، إيجار، نثريات...)
        $table->date('expense_date'); // تاريخ الصرف
        $table->text('notes')->nullable(); // ملاحظات إضافية
        $table->foreignId('salary_payment_id')->nullable()->constrained('salary_payments')->onDelete('cascade');
        $table->timestamps();
    });

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
