<?php

use FHusquinet\CampaignActivityTracker\CampaignActivityTracker;

if (! function_exists('campaignActivityTracker')) {
    function campaignActivityTracker($event = '')
    {
        return app(CampaignActivityTracker::class)->forEvent($event);
    }
}