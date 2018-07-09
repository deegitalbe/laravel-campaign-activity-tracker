<?php

namespace FHusquinet\CampaignActivityTracker\Traits;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;

trait TracksCampaignActivity
{

    protected $campaignActivityTrackingEnabled = true;
    
    protected static function bootTracksCampaignActivity()
    {
        static::eventsTracked()->each(function ($eventName) {
            return static::$eventName(function ($model) use ($eventName) {
                if (! $model->campaignActivityTrackingEnabled()) {
                    return;
                }

                campaignActivityTracker($eventName)
                    ->performedOn($model)
                    ->track();
            });
        });
    }

    public function campaignActivities()
    {
        return $this->morphMany(CampaignActivity::class, 'subject');
    }

    public function campaignActivity()
    {
        return $this->campaignActivities()->latest()->first();
    }

    /*
     * Get the event names that should be recorded.
     */
    protected static function eventsTracked()
    {
        if ( isset(static::$campaignTrackingEvents) ) {
            return collect(static::$campaignTrackingEvents);
        }

        $events = collect(config('campaign-activity-tracker.events'));

        return $events;
    }

    public function disableCampaignActivityTracking()
    {
        $this->campaignActivityTrackingEnabled = false;
        return $this;
    }
    
    public function enableCampaignActivityTracking()
    {
        $this->campaignActivityTrackingEnabled = true;
        return $this;
    }
    
    public function campaignActivityTrackingEnabled()
    {
        return $this->campaignActivityTrackingEnabled;
    }

}