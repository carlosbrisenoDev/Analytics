<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsPerformance extends Model
{
    protected $fillable = ['site_id', 'date', 'performance_score', 'core_web_vitals'];
    protected $casts = ['core_web_vitals' => 'array', 'date' => 'date'];
    public function site() { return $this->belongsTo(Site::class); }
}
