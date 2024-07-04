<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Redis;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind(Redis::class, function () {
            return new Redis([
                'host' => config('database.redis.default.host'),
                'port' => (int) config('database.redis.default.port'),
            ]);
        });
    }
}
