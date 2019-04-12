<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'msw_id',
        'country_name',
        'region_name',
        'wave_break',
        'msw_wave_break_slug',
        'slug',
        'latitude',
        'longitude',
        'description',
    ];

    protected $table = 'locations';

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
