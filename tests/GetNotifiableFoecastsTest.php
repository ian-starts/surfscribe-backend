<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class GetNotifiableFoecastsTest extends TestCase
{
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function itCanFindNotifiableForecasts()
    {
        $result = $this->artisan('surfscribe:check_forecasts');
        $this->assertGreaterThan(1,count($result));
    }
}
