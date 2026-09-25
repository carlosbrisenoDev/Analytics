<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['site_name', 'type', 'session_id', 'region', 'duration', 'content', 'x_coord', 'y_coord'])]
class Event extends Model
{
}
