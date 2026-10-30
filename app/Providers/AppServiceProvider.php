<?php

namespace App\Providers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Profile;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        // Short, stable morph aliases. "grade"/"class"/"profile" are also the audience scope types.
        Relation::enforceMorphMap([
            'user' => User::class,
            'grade' => Grade::class,
            'class' => SchoolClass::class,
            'profile' => Profile::class,
            'exam' => Exam::class,
            'material' => Material::class,
        ]);

        RateLimiter::for('registration', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('uploads', fn (Request $request) => Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()));
    }
}
