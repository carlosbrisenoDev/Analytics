<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = ['name', 'domain', 'api_key'];

    public function traffic() { return $this->hasMany(AnalyticsTraffic::class); }
    public function seo() { return $this->hasMany(AnalyticsSeo::class); }
    public function performance() { return $this->hasMany(AnalyticsPerformance::class); }
    public function meta() { return $this->hasMany(AnalyticsMeta::class); }
}
