<?php

namespace App\Providers;

use App\Repository\Telegram\TelegramMock;
use App\Repository\Telegram\Telegram;
use App\Repository\Telegram\TelegramInterface;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
/**
 * Register any application services.
 *
 * @return void
 */
//public function register()
//{
// $this->app->singleton(TelegramInterface::class, function () {
//     if (!env("TELEGRAM_STATUS")) {
//         $telegram = TelegramMock::instance();
//     } else {
//         $telegram = Telegram::instance();
//     }
//     return $telegram;
// });

// $this->app->singleton(\Faker\Generator::class, function () {
//     return \Faker\Factory::create('ru_RU');
// });
//}

/**
 * Bootstrap any application services.
 *
 * @return void
 */
// public function boot()
// {
//     Paginator::useBootstrap();
//     if (env('FORCE_SCHEME', true)) {
//         URL::forceScheme('http');
//     }
// }
}