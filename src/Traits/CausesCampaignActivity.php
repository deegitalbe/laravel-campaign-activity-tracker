<?php

namespace FHusquinet\CampaignActivityTracker\Traits;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;

trait CausesCampaignActivity
{

    public function campaignActivities()
    {
        return $this->morphMany(CampaignActivity::class, 'causer');
    }

    public function campaignActivity()
    {
        return $this->campaignActivities()->latest()->first();
    }
}