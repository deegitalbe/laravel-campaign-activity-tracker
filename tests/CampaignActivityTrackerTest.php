<?php

namespace FHusquinet\CampaignActivityTracker\Tests;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;
use FHusquinet\CampaignActivityTracker\Tests\Models\User;
use FHusquinet\CampaignActivityTracker\Tests\Models\Article;

use Auth;

class CampaignActivityTrackerTest extends TestCase
{

    /** @test */
    public function it_can_create_an_activity()
    {
        $data = $this->getTrackingData();

        campaignActivityTracker()->createActivity($data);
        
        $this->assertCampaignActivitiesTableHas([
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }

    /** @test */
    public function it_can_create_an_activity_with_the_event_responsible_for_the_tracking()
    {
        $data = $this->getTrackingData();

        campaignActivityTracker('updated')->createActivity($data);
        
        $this->assertCampaignActivitiesTableHas([
            'event'      => 'updated',
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }
    
    /** @test */
    public function it_will_not_create_an_activity_if_marked_as_disabled()
    {
        config(['campaign-activity-tracker.enabled' => false]);
        $data = $this->getTrackingData();

        campaignActivityTracker()->createActivity($data);
        
        $this->assertTrue(CampaignActivity::all()->isEmpty());
    }

    /** @test */
    public function the_authed_user_will_be_set_as_the_causer_when_creating_an_activity()
    {
        $userId = 1;
        Auth::login( User::find($userId) );
        $data = $this->getTrackingData();

        campaignActivityTracker()->createActivity($data);
        
        $this->assertCampaignActivitiesTableHas([
            'causer_id'  => $userId,
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }

    /** @test */
    public function a_causer_can_be_set_when_creating_an_activity()
    {
        $user = User::first();
        $data = $this->getTrackingData();

        campaignActivityTracker()->causedBy($user)->createActivity($data);
        
        $this->assertCampaignActivitiesTableHas([
            'causer_id'  => $user->id,
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }

    /** @test */
    public function a_causer_can_be_set_using_the_short_named_method_when_creating_an_activity()
    {
        $user = User::first();
        $data = $this->getTrackingData();

        campaignActivityTracker()->by($user)->createActivity($data);
        
        $this->assertCampaignActivitiesTableHas([
            'causer_id'  => $user->id,
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }

    /** @test */
    public function a_performed_on_model_can_be_set_when_creating_an_activity()
    {
        $article = Article::first();
        $data = $this->getTrackingData();

        campaignActivityTracker()->performedOn($article)->createActivity($data);
        
        $this->assertCampaignActivitiesTableHas([
            'subject_id' => $article->id,
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }

    /** @test */
    public function a_performed_on_model_can_be_set_using_the_short_named_method_when_creating_an_activity()
    {
        $article = Article::first();
        $data = $this->getTrackingData();

        campaignActivityTracker()->performedOn($article)->createActivity($data);
        
        $this->assertCampaignActivitiesTableHas([
            'subject_id' => $article->id,
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }

    /** @test */
    public function it_will_return_the_activity_model_when_creating_one()
    {
        $data = $this->getTrackingData();

        $campaignActivityModel = campaignActivityTracker()->createActivity($data);
        
        $this->assertInstanceOf(CampaignActivity::class, $campaignActivityModel);
    }

    /** @test */
    public function it_can_track_one_campaign_using_the_session()
    {
        $data = $this->getTrackingData();
        session()->put('campaign_activities', $data);

        campaignActivityTracker()->track();
        
        $this->assertCampaignActivitiesTableHas([
            'parameters' => json_encode($data['parameters']),
            'url'        => $data['url'],
            'visited_at' => $data['visited_at']
        ]);
    }

    /** @test */
    public function it_can_track_multiple_campaign_using_the_session()
    {
        $data1 = $this->getTrackingData();
        $data2 = $this->getSecondTrackingData();
        session()->put('campaign_activities', [$data1, $data2]);

        campaignActivityTracker()->track();
        
        $this->assertCampaignActivitiesTableHas([
            'parameters' => json_encode($data1['parameters']),
            'url'        => $data1['url'],
            'visited_at' => $data1['visited_at']
        ]);
        $this->assertCampaignActivitiesTableHas([
            'parameters' => json_encode($data2['parameters']),
            'url'        => $data2['url'],
            'visited_at' => $data2['visited_at']
        ]);
    }

    /** @test */
    public function tracking_a_campaign_will_return_a_collection_containing_the_activities()
    {
        $data1 = $this->getTrackingData();
        $data2 = $this->getSecondTrackingData();
        session()->put('campaign_activities', [$data1, $data2]);

        $activities = campaignActivityTracker()->track();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $activities);
        $this->assertEquals($activities->count(), 2);
    }
}
