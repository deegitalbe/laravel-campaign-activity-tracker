<?php

namespace FHusquinet\CampaignActivityTracker;

use Illuminate\Support\ServiceProvider;

class CampaignActivityTrackerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/campaign-activity-tracker.php' => config_path('campaign-activity-tracker.php'),
            ], 'config');

            /*
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'campaign-activity-tracker');

            $this->publishes([
                __DIR__.'/../resources/views' => base_path('resources/views/vendor/campaign-activity-tracker'),
            ], 'views');
            */
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'campaign-activity-tracker');
    }
}
