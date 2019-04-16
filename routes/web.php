<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api/auth'], function () use ($router) {
    $router->post('login', 'Auth\AuthController@login');
    $router->post('signup', 'Auth\AuthController@signup');
});

$router->group(['prefix' => 'api', 'middleware' =>'cache'], function () use ($router) {
    $router->get('locations', 'Locations\LocationsController@index');
    $router->get('forecasts/{id}', 'Forecasts\ForecastsController@location');
});


$router->group(['middleware' => 'jwt.auth', 'prefix'=>'api'], function () use ($router) {
    $router->post('notifications', 'Notifications\NotificationsController@add');
    $router->put('notifications/{uuid}', 'Notifications\NotificationsController@edit');
    $router->get('notifications', 'Notifications\NotificationsController@get');
    $router->delete('notifications/{uuid}', 'Notifications\NotificationsController@delete');

    $router->get('auth/user', function ($request) {
        return (new \App\Factories\CamelCaseJsonResponseFactory())->json($request->auth);
    });
});
