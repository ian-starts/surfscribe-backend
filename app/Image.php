<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = [
        'location_id',
        'dimensions',
    ];

    protected $casts = [
        'dimensions' => 'array'
    ];

    protected $table = 'images';

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
