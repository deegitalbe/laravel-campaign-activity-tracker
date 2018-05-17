<?php

namespace FHusquinet\CampaignActivityTracker;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Contracts\Config\Repository;

class CampaignActivityTracker
{
    use Macroable;

    /** @var \Illuminate\Auth\AuthManager */
    protected $auth;

    /** @var bool */
    protected $trackingEnabled;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $performedOn;
    
    /** @var \Illuminate\Database\Eloquent\Model */
    protected $causedBy;

    protected $event;

    /**
     * Create a new CampaignActivityTracker Instance.
     */
    public function __construct(AuthManager $auth, Repository $config)
    {
        $this->auth = $auth;
        $this->causedBy = $auth->guard( $this->auth->getDefaultDriver() )->user();
        $this->trackingEnabled = $config['campaign-activity-tracker']['enabled'] ?? true;
    }

    public function forEvent($event)
    {
        $this->event = $event;
        return $this;
    }

    public function performedOn($model)
    {
        $this->performedOn = $model;
        return $this;
    }

    public function on($model)
    {
        return $this->performedOn($model);
    }

    public function causedBy($model)
    {
        $this->causedBy = $model;
        return $this;
    }

    public function by($model)
    {
        return $this->causedBy($model);
    }

    /**
     * @param string $description
     *
     * @return null|mixed
     */
    public function track()
    {
        if (! $this->trackingEnabled) {
            return;
        }

        $trackedActivities = session()->get('campaign_activities') ?? [];
        if ( isset($trackedActivities['parameters']) ) {
            $trackedActivities = [$trackedActivities];
        }
        $activities = [];

        foreach ( $trackedActivities as $data ) {
            $activity = $this->createActivity($data);
            $activities[] = $activity;
        }
        
        return collect($activities);
    }

    public function createActivity(array $data)
    {
        if (! $this->trackingEnabled) {
            return;
        }

        $activity = new CampaignActivity();
            
        if ($this->performedOn) {
            $activity->subject()->associate($this->performedOn);
        }
        
        if ($this->causedBy) {
            $activity->causer()->associate($this->causedBy);
        }
        
        if ($this->event) {
            $activity->event = $this->event;
        }
        
        $activity->parameters = $data['parameters'] ?? null;
        $activity->url        = $data['url'] ?? null;
        $activity->visited_at = $data['visited_at'] ?? null;
        $activity->save();

        return $activity;
    }
}
