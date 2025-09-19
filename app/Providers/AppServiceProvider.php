<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        if (config('logging.db_queries')) {
            DB::listen(function ($query) {
                if (str_starts_with($query->sql, "select * from \"jobs\"")) return;
                $log = log10($query->time) + 1;
                $magnitude = $log < 0 ? 0 : round($log);
                $warnChars = str_pad(str_repeat(">", $magnitude), 5, " ");
                $seconds = number_format($query->time / 1000, 3);
                $bindings = json_encode($query->bindings);
                Log::channel("queries")->info(
                    "$warnChars ($seconds) | $query->sql | $bindings"
                );
            });
        }

        Model::unguard();
    }
}
