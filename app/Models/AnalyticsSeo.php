<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSeo extends Model
{
    protected $fillable = ['site_id', 'date', 'impressions', 'clicks', 'ctr', 'position'];
    protected $casts = ['date' => 'date'];
    public function site() { return $this->belongsTo(Site::class); }
}
