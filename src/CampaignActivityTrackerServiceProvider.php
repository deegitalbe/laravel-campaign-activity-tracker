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

            if (! class_exists('CreateCampaignActivitiesTable')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../migrations/create_campaign_activities_table.php.stub' => database_path("/migrations/{$timestamp}_create_campaign_activities_table.php"),
                ], 'migrations');
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/campaign-activity-tracker.php', 'campaign-activity-tracker');
    }
}
