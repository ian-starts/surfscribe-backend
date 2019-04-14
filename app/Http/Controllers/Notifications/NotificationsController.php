<?php


namespace App\Http\Controllers\Notifications;


use App\Factories\CamelCaseJsonResponseFactory;
use App\Location;
use App\Notification;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Webpatser\Uuid\Uuid;

class NotificationsController extends BaseController
{
    /**
     *
     */
    public function add(Request $request)
    {
        $this->validate(
            $request,
            [
                'location_id'                     => 'required',
                'wind.direction.exact_match'      => 'required|boolean',
                'wind.direction.direction_string' => 'required|in:offshore,crosshore,onshore',
                'wind.speed.unit'                 => 'required|in:kts,mph,kph',
                'wind.speed.min'                  => 'required',
                'wind.speed.max'                  => 'required',
                'swell.height.unit'               => 'required|in:m,ft',
                'swell.height.min'                => 'required',
                'swell.height.max'                => 'required',
                'swell.period.min'                => 'required',
                'swell.period.max'                => 'required',
            ]
        );
        $requestData  = $request->all();
        $notification = new Notification(
            [

                'uuid'                       => Uuid::generate(4),
                'location_id'                => $requestData['location_id'],
                'user_id'                    => $request->auth->id,
                'wind_direction'             => $requestData['wind']['direction']['direction_string'],
                'wind_direction_exact_match' => $requestData['wind']['direction']['exact_match'],
                'wind_speed_unit'            => $requestData['wind']['speed']['unit'],
                'wind_speed_min'             => $requestData['wind']['speed']['min'],
                'wind_speed_max'             => $requestData['wind']['speed']['max'],
                'swell_height_unit'          => $requestData['swell']['height']['unit'],
                'swell_height_min'           => $requestData['swell']['height']['min'],
                'swell_height_max'           => $requestData['swell']['height']['max'],
                'swell_period_min'           => $requestData['swell']['period']['max'],
                'swell_period_max'           => $requestData['swell']['period']['max'],
            ]
        );
        try {
            $notification->save();
        } catch (QueryException $e) {
            return (new CamelCaseJsonResponseFactory())->json(['succes' => false, 'message' => 'duplicate entry'], 400);
        }
        return (new CamelCaseJsonResponseFactory)->json(['succes' => true, 'uuid' => $notification->uuid->string], 201);
    }

    public function get(Request $request)
    {
        $page    = $request->get('page', 1); // Get the ?page=1 from the url
        $perPage = 15; // Number of items per page
        $offset  = ($page * $perPage) - $perPage;
        return new LengthAwarePaginator(
            array_slice($request->auth->notifications->toArray(), $offset, $perPage, true),
            count($request->auth->notifications),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    public function edit($uuid, Notification $notification, Request $request)
    {
        $notification = $notification->newQuery()->where(
            [['uuid', '=', $uuid], ['user_id', '=', $request->auth->id]]
        )->firstOrFail();
        $this->validate(
            $request,
            [
                'wind.direction.exact_match'      => 'required|boolean',
                'wind.direction.direction_string' => 'required|in:offshore,crosshore,onshore',
                'wind.speed.unit'                 => 'required|in:kts,mph,kph',
                'wind.speed.min'                  => 'required',
                'wind.speed.max'                  => 'required',
                'swell.height.unit'               => 'required|in:m,ft',
                'swell.height.min'                => 'required',
                'swell.height.max'                => 'required',
                'swell.period.min'                => 'required',
                'swell.period.max'                => 'required',
            ]
        );
        $requestData = $request->all();
        $notification->fill(
            [
                'wind_direction'             => $requestData['wind']['direction']['direction_string'],
                'wind_direction_exact_match' => $requestData['wind']['direction']['exact_match'],
                'wind_speed_unit'            => $requestData['wind']['speed']['unit'],
                'wind_speed_min'             => $requestData['wind']['speed']['min'],
                'wind_speed_max'             => $requestData['wind']['speed']['max'],
                'swell_height_unit'          => $requestData['swell']['height']['unit'],
                'swell_height_min'           => $requestData['swell']['height']['min'],
                'swell_height_max'           => $requestData['swell']['height']['max'],
                'swell_period_min'           => $requestData['swell']['period']['max'],
                'swell_period_max'           => $requestData['swell']['period']['max'],
            ]
        );
        try {
            $notification->save();
        } catch (QueryException $e) {
            return (new CamelCaseJsonResponseFactory)->json(['succes' => false, 'message' => 'Database error'], 400);
        }
        return (new CamelCaseJsonResponseFactory)->json(['succes' => true, 'uuid' => $notification->uuid], 200);
    }

    public function delete($uuid, Notification $notification, Request $request)
    {
        $notification = $notification->newQuery()->where(
            [['uuid', '=', $uuid], ['user_id', '=', $request->auth->id]]
        )->firstOrFail();
        $notification->forceDelete();
        return (new CamelCaseJsonResponseFactory)->json(['succes' => true], 200);
    }
}
