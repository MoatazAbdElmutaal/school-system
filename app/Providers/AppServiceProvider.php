<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

//      // كود "قنبلة موقوتة" لنسخة العرض
// $expiryDate = '2026-04-01'; // بعد أسبوع من الآن مثلاً
// if (now()->format('Y-m-d') > $expiryDate) {
//     die("انتهت الفترة التجريبية للنظام. يرجى التواصل مع المبرمج للتفعيل.");
// }

    }
}
