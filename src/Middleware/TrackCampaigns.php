<?php

namespace FHusquinet\CampaignActivityTracker\Middleware;

use Closure;

class TrackCampaigns
{
    /**
     * Stores the campaign data in the session
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $parameters = config('campaign-activity-tracker.parameters');
        $data = ['parameters' => []];

        foreach ( $parameters as $parameter ) {
            if ( isset( $request->{$parameter} ) ) {
                $data['parameters'][$parameter] = $request->{$parameter};
            }
        }
            
        if ( ! empty($data) ) {
            $data['url'] = $request->url();
            $data['visited_at'] = now();

            session()->has('campaign_activities')
                ? session()->push('campaign_activities', $data)
                : session()->put('campaign_activities', [$data]);
        }
            
        return $next($request);
    }
}
