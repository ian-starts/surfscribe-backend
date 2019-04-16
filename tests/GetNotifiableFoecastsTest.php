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
//        $result = $this->artisan('surfscribe:check_forecasts');
//        $this->assertGreaterThan(1,count($result));
        \Illuminate\Support\Facades\Mail::to('hello@yonikok.com')->send(
            new \App\Mail\MailNotifications(new \App\User(), [], 'The weather is looking good these days!')
        );
    }
}
