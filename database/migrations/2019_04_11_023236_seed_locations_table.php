<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use IanKok\MSWSDK\Client\MSWClient;
use IanKok\MSWSDK\Spots\SpotsRepository;
use IanKok\MSWSDK\Spots\SpotsMapper;

class SeedLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $client    = new MSWClient('https://magicseaweed.com/');
        $repo      = new SpotsRepository($client, new SpotsMapper());
        $locations = $repo->list();
        foreach ($locations as $location) {
            (new \App\Location(
                [
                    'msw_id'              => $location->getId(),
                    'country_name'        => $location->getCountry()->getName(),
                    'region_name'         => $location->getRegion()->getName(),
                    'wave_break'          => $location->getName(),
                    'msw_wave_break_slug' => $location->getUrl(),
                    'slug'                => \Illuminate\Support\Str::slug(
                        $location->getCountry()->getName() . ' ' . $location->getName()
                    ),
                    'longitude'           => $location->getLon(),
                    'latitude'            => $location->getLat(),
                    'description'         => $location->getDescription(),

                ]
            ))->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Location::query()->delete();
    }
}
