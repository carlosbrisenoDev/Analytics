<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageAnalysis extends Model
{
    protected $table = 'page_analyses';

    protected $fillable = [
        'site_name',
        'url',
        'path',
        'http_status',
        'has_ga4',
        'has_gtm',
        'has_meta_pixel',
        'has_search_console',
        'has_schema_org',
        'schema_org_types',
        'detection_details',
        'fetch_error',
        'analyzed_at',
    ];

    protected $casts = [
        'has_ga4' => 'boolean',
        'has_gtm' => 'boolean',
        'has_meta_pixel' => 'boolean',
        'has_search_console' => 'boolean',
        'has_schema_org' => 'boolean',
        'detection_details' => 'array',
        'analyzed_at' => 'datetime',
        'http_status' => 'integer',
    ];
}
