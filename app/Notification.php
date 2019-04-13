<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'uuid',
        'location_id',
        'user_id',
        'wind_direction',
        'wind_direction_exact_match',
        'wind_speed_unit',
        'wind_speed_min',
        'wind_speed_max',
        'swell_height_unit',
        'swell_height_min',
        'swell_height_max',
        'swell_period_min',
        'swell_period_max',
    ];

    protected $table = 'notifications';

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
