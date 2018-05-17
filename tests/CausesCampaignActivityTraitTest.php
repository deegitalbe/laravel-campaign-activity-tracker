<?php

namespace FHusquinet\CampaignActivityTracker\Tests;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;
use FHusquinet\CampaignActivityTracker\Traits\CausesCampaignActivity;
use FHusquinet\CampaignActivityTracker\Traits\TracksCampaignActivity;
use FHusquinet\CampaignActivityTracker\Tests\Models\User;
use FHusquinet\CampaignActivityTracker\Tests\Models\Article;

use Auth;

class CausesCampaignActivityTraitTest extends TestCase
{

    protected $user;

    public function setUp()
    {
        parent::setUp();
        
        $this->article = new class() extends Article {
            use TracksCampaignActivity;
        };
        
        $this->user = new class() extends User {
            use CausesCampaignActivity;
        };
        
        $this->assertCount(0, CampaignActivity::all());
    }

    /** @test */
    public function a_model_can_access_the_campaign_activities_he_caused()
    {
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $user = $this->createUser();
        Auth::login($user);

        $article = $this->createArticle();
        $article->name = 'updated data';
        $article->save();

        $this->assertCount(2, $user->campaignActivities);
        $this->assertEquals('updated', $user->campaignActivities->last()->event);
    }

    /** @test */
    public function a_model_can_access_his_last_campaign_activity()
    {
        session()->put('campaign_activities', [$this->getTrackingData()]);
        $user = $this->createUser();
        Auth::login($user);

        $article = $this->createArticle();

        $this->assertEquals('created', $user->campaignActivity()->event);
    }

    protected function createArticle()
    {
        $article = new $this->article();
        $article->name = 'my article';
        $article->save();
        return $article;
    }

    protected function createUser()
    {
        $user = new $this->user();
        $user->name = 'my user name';
        $user->save();
        return $user;
    }
}