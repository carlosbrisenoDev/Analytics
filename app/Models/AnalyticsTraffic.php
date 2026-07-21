<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsTraffic extends Model
{
    protected $table = 'analytics_traffic';
    protected $fillable = ['site_id', 'date', 'users', 'sessions', 'conversions', 'avg_time_seconds', 'funnel_data'];
    protected $casts = ['funnel_data' => 'array', 'date' => 'date'];

    public function site() { return $this->belongsTo(Site::class); }
}
