<?php

namespace FHusquinet\CampaignActivityTracker\Tests;

use FHusquinet\CampaignActivityTracker\Models\CampaignActivity;
use FHusquinet\CampaignActivityTracker\Tests\Models\User;
use Illuminate\Database\Schema\Blueprint;
use FHusquinet\CampaignActivityTracker\Tests\Models\Article;
use FHusquinet\CampaignActivityTracker\CampaignActivityTrackerServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function checkRequirements()
    {
        parent::checkRequirements();

        collect($this->getAnnotations())->filter(function ($location) {
            return in_array('!Travis', array_get($location, 'requires', []));
        })->each(function ($location) {
            getenv('TRAVIS') && $this->markTestSkipped('Travis will not run this test.');
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            CampaignActivityTrackerServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');

        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => $this->getTempDirectory().'/database.sqlite',
            'prefix' => '',
        ]);

        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('app.key', '6rE9Nz59bGRbeMATftriyQjrpF7DcOQm');
    }

    protected function setUpDatabase()
    {
        $this->resetDatabase();

        $this->createCampaignActivitiesTable();

        $this->createTables('articles', 'users');
        $this->seedModels(Article::class, User::class);
    }

    protected function resetDatabase()
    {
        file_put_contents($this->getTempDirectory().'/database.sqlite', null);
    }

    protected function createCampaignActivitiesTable()
    {
        include_once '__DIR__'.'/../migrations/create_campaign_activities_table.php.stub';

        (new \CreateCampaignActivitiesTable())->up();
    }

    public function getTempDirectory(): string
    {
        return __DIR__.'/temp';
    }

    protected function createTables(...$tableNames)
    {
        collect($tableNames)->each(function (string $tableName) {
            $this->app['db']->connection()->getSchemaBuilder()->create($tableName, function (Blueprint $table) use ($tableName) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('text')->nullable();
                $table->timestamps();
                $table->softDeletes();

                if ($tableName === 'articles') {
                    $table->integer('user_id')->unsigned()->nullable();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                    $table->text('json')->nullable();
                }
            });
        });
    }

    protected function seedModels(...$modelClasses)
    {
        collect($modelClasses)->each(function (string $modelClass) {
            foreach (range(1, 0) as $index) {
                $modelClass::create(['name' => "name {$index}"]);
            }
        });
    }

    public function getTrackingData()
    {
        return [
            'parameters' => [
                'utm_source'   => 'registered-users',
                'utm_medium'   => 'newsletter',
                'utm_campaign' => 'new-feature-launch',
                'utm_content'  => 'footer-cta-link'
            ],
            'url'        => 'http://www.my-tracked-site.com/new-feature',
            'visited_at' => now()
        ];
    }

    public function getSecondTrackingData()
    {
        return [
            'parameters' => [
                'utm_source'   => 'new-users',
                'utm_medium'   => 'newsletter',
                'utm_campaign' => 'welcome',
                'utm_content'  => 'main-cta-button'
            ],
            'url'        => 'http://www.my-tracked-site.com/welcome-to-my-site',
            'visited_at' => now()
        ];
    }

    public function assertCampaignActivitiesTableHas(array $data = [])
    {
        return $this->assertDatabaseHas(config('campaign-activity-tracker.table_name'), $data);
    }

    /**
     * @return \FHusquinet\CampaignActivityTracker\Models\CampaignActivity|null
     */
    public function getFirstCampaignActivity()
    {
        return CampaignActivity::all()->first();
    }

    /**
     * @return \FHusquinet\CampaignActivityTracker\Models\CampaignActivity|null
     */
    public function getLastCampaignActivity()
    {
        return CampaignActivity::all()->last();
    }

    public function doNotMarkAsRisky()
    {
        $this->assertTrue(true);
    }
}