<?php

return [

    /**
     * Change the default table name.
     */
    'table_name' => 'campaign_activities',

    /**
     * If set to false, no campaign will be tracked.
     */
    'enabled' => env('CAMPAIGN_ACTIVITY_TRACKER_ENABLE', true),

    /**
     * Which GET parameters should be tracked.
     */
    'parameters' => [
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'gclid'
    ],

    /**
     * Which events should be logged by default.
     * You can change it on each model using the TracksCampaigns Trait.
     */
    'events' => [
        'created',
        'updated',
        'deleted'
    ]
];