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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
