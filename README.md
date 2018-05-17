# Track the activity of your campaigns on your Laravel application

[![Latest Version on Packagist](https://img.shields.io/packagist/v/fhusquinet/laravel-campaign-activity-tracker.svg?style=flat-square)](https://packagist.org/packages/fhusquinet/laravel-campaign-activity-tracker)
[![Total Downloads](https://img.shields.io/packagist/dt/fhusquinet/laravel-campaign-activity-tracker.svg?style=flat-square)](https://packagist.org/packages/fhusquinet/laravel-campaign-activity-tracker)

If you are managing AdWords or Facebook campaigns using UTM parameters in your URLs this package might be for you! Simply add the TracksCampaignActivity trait on your wanted models and the TrackCampaigns Middleware to your wanted routes and see what impact your campaigns have on your data.
It will track the created, updated and deleted event on the models using the TracksCampaignActivity and store the UTM parameters, url and time of visit of each different instance in the database.

## Installation

You can install the package via composer:

```bash
composer require fhusquinet/laravel-campaign-activity-tracker
```

## Usage

Add the TracksCampaignActivity trait to your wanted models.
``` php
// App\Models\Article.php

namespace App\Models;

use FHusquinet\CampaignActivityTracker\Traits\TracksCampaignActivity;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use TracksCampaignActivity
```

And add the TrackCampaigns middleware to your wanted routes, you can either set it at the global level or on a route basis.
``` php
\\ App/Http/Kernel.php

/**
 * The application's route middleware groups.
 *
 * @var array
 */
protected $middlewareGroups = [
    'web' => [
        //
        \FHusquinet\CampaignActivityTracker\Middleware\TrackCampaigns::class
    ],
```

``` php
\\ App/Http/Kernel.php

protected $routeMiddleware = [
    //
    'campaignTracker' => \FHusquinet\CampaignActivityTracker\Middleware\TrackCampaigns::class
];

// routes/web.php
Route::get('/')->middleware('campaignTracker');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email florian.husquinet@deegital.be instead of using the issue tracker.

## Thanks

Special thanks to Spatie for their awesome [laravel-activitylog package](https://github.com/spatie/laravel-activitylog) as well as their [skeleton-php package](https://github.com/spatie/skeleton-php) for getting me the inspiration and help required for this package!

## Credits

- [Florian Husquinet](https://github.com/fhusquinet)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
