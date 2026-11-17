<?php

namespace App\Providers;

use App\Enums\TaskStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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
        View::composer('components.topbar', function ($view) {
            if (! Auth::check()) {
                return $view->with('topbarTasks', collect())->with('topbarTaskCount', 0);
            }

            $user = Auth::user();
            $soon = today()->addDays(3)->toDateString();

            $tasks = $user->tasks()
                ->where('status', '!=', TaskStatus::Completed->value)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<=', $soon)
                ->orderBy('due_date')
                ->orderBy('due_time')
                ->limit(5)
                ->get();

            $count = $user->tasks()
                ->where('status', '!=', TaskStatus::Completed->value)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<=', $soon)
                ->count();

            $view->with('topbarTasks', $tasks)->with('topbarTaskCount', $count);
        });
    }
}
