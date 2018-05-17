<?php

namespace FHusquinet\CampaignActivityTracker\Tests;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;
use FHusquinet\CampaignActivityTracker\Traits\TracksCampaignActivity;
use FHusquinet\CampaignActivityTracker\Tests\Models\User;
use FHusquinet\CampaignActivityTracker\Tests\Models\Article;

use Auth;

class TracksCampaignActivityTraitTest extends TestCase
{

    protected $article;

    public function setUp()
    {
        parent::setUp();
        
        $this->article = new class() extends Article {
            use TracksCampaignActivity;
        };
        
        $this->assertCount(0, CampaignActivity::all());
    }

    /** @test */
    public function a_model_has_tracking_enabled_by_default()
    {
        $this->assertTrue($this->article->campaignActivityTrackingEnabled());
    }

    /** @test */
    public function a_model_can_disable_and_enable_tracking()
    {
        $this->article->disableCampaignActivityTracking();
        $this->assertFalse($this->article->campaignActivityTrackingEnabled());

        $this->article->enableCampaignActivityTracking();
        $this->assertTrue($this->article->campaignActivityTrackingEnabled());
    }

    /** @test */
    public function a_model_will_be_tracked_when_created_updated_and_deleted()
    {
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $article = $this->createArticle();

        $this->assertCount(1, CampaignActivity::all());
        $this->assertEquals('created', $this->getLastCampaignActivity()->event);

        $article->name = 'updated data';
        $article->save();

        $this->assertCount(2, CampaignActivity::all());
        $this->assertEquals('updated', $this->getLastCampaignActivity()->event);

        $article->delete();

        $this->assertCount(3, CampaignActivity::all());
        $this->assertEquals('deleted', $this->getLastCampaignActivity()->event);
    }

    /** @test */
    public function a_model_will_be_tracked_when_created_updated_and_deleted_and_if_he_wants_to_be()
    {
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $article = $this->createArticle();

        $this->assertCount(1, CampaignActivity::all());
        $this->assertEquals('created', $this->getLastCampaignActivity()->event);

        $article->disableCampaignActivityTracking();

        $article->name = 'updated data';
        $article->save();

        $this->assertCount(1, CampaignActivity::all());
        $this->assertEquals('created', $this->getLastCampaignActivity()->event);

        $article->enableCampaignActivityTracking();

        $article->delete();

        $this->assertCount(2, CampaignActivity::all());
        $this->assertEquals('deleted', $this->getLastCampaignActivity()->event);
    }

    /** @test */
    public function a_model_can_defined_on_which_events_he_wants_to_be_tracked()
    {
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $articleClass = new class() extends Article {
            use TracksCampaignActivity;

            protected static $campaignTrackingEvents = ['updated'];
        };
        $article = new $articleClass();
        $article->save();

        $this->assertCount(0, CampaignActivity::all());

        $article->name = 'updated data';
        $article->save();

        $this->assertCount(1, CampaignActivity::all());
        $this->assertEquals('updated', $this->getLastCampaignActivity()->event);

        $article->delete();

        $this->assertCount(1, CampaignActivity::all());
        $this->assertEquals('updated', $this->getLastCampaignActivity()->event);
    }

    /** @test */
    public function a_model_will_not_be_tracked_if_the_tracked_is_disabled()
    {
        config(['campaign-activity-tracker.enabled' => false]);
        
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $article = $this->createArticle();
        
        $this->assertCount(0, CampaignActivity::all());
    }

    /** @test */
    public function a_model_can_access_his_campaign_activities()
    {
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $article = $this->createArticle();
        $article->name = 'updated data';
        $article->save();

        $this->assertCount(2, $article->campaignActivities);
        $this->assertEquals('updated', $article->campaignActivities->last()->event);
    }

    /** @test */
    public function a_model_can_access_his_last_campaign_activity()
    {
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $article = $this->createArticle();

        $this->assertEquals('created', $article->campaignActivity()->event);
    }

    protected function createArticle()
    {
        $article = new $this->article();
        $article->name = 'my article';
        $article->save();
        return $article;
    }
}