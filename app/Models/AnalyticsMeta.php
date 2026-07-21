<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsMeta extends Model
{
    protected $fillable = ['site_id', 'date', 'events', 'conversions'];
    protected $casts = ['date' => 'date'];
    public function site() { return $this->belongsTo(Site::class); }
}
