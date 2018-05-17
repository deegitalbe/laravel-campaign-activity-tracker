<?php

namespace FHusquinet\CampaignActivityTracker\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $guarded = [];
    public function User()
    {
        return $this->belongsTo(User::class);
    }
}