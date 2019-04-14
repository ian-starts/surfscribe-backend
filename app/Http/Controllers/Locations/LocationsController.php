<?php


namespace App\Http\Controllers\Locations;


use App\Factories\CamelCaseJsonResponseFactory;
use App\Filters\LocationsFilter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;

class LocationsController extends BaseController
{
    public function index(Request $request, LocationsFilter $filter)
    {
        return (new CamelCaseJsonResponseFactory)->json($filter->paginate($request->get('pagesize', 50)),200,[]);
    }

    public function location ($id, LocationsFilter $filter)
    {
        return (new CamelCaseJsonResponseFactory)->json($filter->query()->where('slug','=', $id)->firstOrFail());
    }
}
