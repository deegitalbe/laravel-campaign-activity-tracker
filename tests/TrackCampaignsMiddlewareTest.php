<?php

namespace FHusquinet\CampaignActivityTracker\Tests;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;
use FHusquinet\CampaignActivityTracker\Middleware\TrackCampaigns;
use FHusquinet\CampaignActivityTracker\Traits\TracksCampaignActivity;
use FHusquinet\CampaignActivityTracker\Tests\Models\User;
use FHusquinet\CampaignActivityTracker\Tests\Models\Article;
use Illuminate\Http\Request;

use Auth;
use Carbon\Carbon;

class TrackCampaignsMiddlewareTest extends TestCase
{

    /** @test */
    public function visiting_an_url_with_utm_get_parameters_will_store_them_in_session()
    {
        Carbon::setTestNow(now());

        $data = $this->getTrackingData();
        $request = Request::create('/', 'GET', $data['parameters']);
        
        $middleware = new TrackCampaigns();
        $middleware->handle($request, function () {});

        $expectedData['parameters'] = $data['parameters'];
        $expectedData['url'] = 'http://localhost';
        $expectedData['visited_at'] = now();

        $this->assertEquals(session()->get('campaign_activities'), [$expectedData]);
    }

    /** @test */
    public function visiting_an_url_with_utm_get_parameters_will_store_them_in_session_along_with_existing_campaigns()
    {
        Carbon::setTestNow(now());

        $data1 = $this->getTrackingData();
        session()->put('campaign_activities', [$data1['parameters']]);

        $data2 = $this->getSecondTrackingData();

        $request = Request::create('/', 'GET', $data2['parameters']);
        
        $middleware = new TrackCampaigns();
        $middleware->handle($request, function () {});

        $expectedData2['parameters'] = $data2['parameters'];
        $expectedData2['url'] = 'http://localhost';
        $expectedData2['visited_at'] = now();

        $campaigns = session()->get('campaign_activities');
        $this->assertCount(2, $campaigns);
        $this->assertEquals($data1['parameters'], $campaigns[0]);
        $this->assertEquals($expectedData2, $campaigns[1]);
    }
}