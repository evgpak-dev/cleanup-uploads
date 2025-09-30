<?php

namespace Evgpak\UploadCleanUp;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Evgpak\UploadCleanUp\Commands\UploadCleanUpCommand;

class UploadCleanUpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->scheduleCommands();
        }

        $this->publishes([
            __DIR__.'/../config/upload-clean-up.php' => config_path('upload-clean-up.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->commands([
            UploadCleanUpCommand::class,
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/../config/upload-clean-up.php', 'upload-clean-up'
        );
    }

    protected function scheduleCommands(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $interval = config('upload-clean-up.schedule_interval');
            $schedule->command('upload:cleanup')->{$interval}();
        });
    }
}
