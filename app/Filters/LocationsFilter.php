<?php


namespace App\Filters;


use App\Location;

class LocationsFilter extends EloquentFilter
{
    const MODEL = Location::class;

    public function filterAll($query)
    {
        $queryBuilder = $this->query();
        $queries      = collect(explode(' ', $query));
        foreach ($queries as $query) {
            $queryBuilder->where(
                function ($queryBuilder) use ($query) {
                    return $queryBuilder->where(
                        'country_name',
                        'like',
                        '%' . $query . '%'
                    )->orWhere(
                        'region_name',
                        'like',
                        '%' . $query . '%'
                    )->orWhere('wave_break', 'like', '%' . $query . '%')
                        ->orWhere('msw_wave_break_slug', 'like', '%' . $query . '%')
                        ->orWhere('slug', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%');
                }
            );
        }
        return $queryBuilder;
    }
}
