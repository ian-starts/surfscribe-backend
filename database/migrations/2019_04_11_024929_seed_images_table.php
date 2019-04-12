<?php

use Illuminate\Database\Migrations\Migration;
use IanKok\MSWSDK\Images\ImagesRepository;
use IanKok\MSWSDK\Client\MSWClient;
use IanKok\MSWSDK\Images\ImagesMapper;

class SeedImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $client    = new MSWClient('https://magicseaweed.com/');
        $repo      = new ImagesRepository($client, new ImagesMapper());
        $locations = \App\Location::all();
        $locations->map(
            function (\App\Location $location) use ($repo) {
                return $repo->getBySpotIdAsync($location->msw_id)->then(
                    function (array $images) use ($location) {
                        foreach ($images as $image) {
                            $jsonDimension = [];
                            foreach ($image->getDimensions() as $dimension) {
                                $jsonDimension[$dimension->getDimension()] = [
                                    'url'    => $dimension->getUrl(),
                                    'width'  => $dimension->getWidth(),
                                    'height' => $dimension->getHeight(),
                                ];
                            }
                            $dbImage = new \App\Image(['location_id' => $location->id, 'dimensions' => $jsonDimension]);
                            $dbImage->save();

                        }
                    }
                )->wait();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
