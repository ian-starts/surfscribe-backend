<?php


namespace App\Filters;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class EloquentFilter extends Filter
{
    const MODEL = null;

    protected $request;
    protected $query;

    public function __construct(
        Request $request = null
    ) {
        $this->request = $request;
        $this->query = call_user_func($this->getModelClassName() . '::query');

        parent::__construct($request);
    }

    public function query(): Builder
    {
        return $this->query;
    }

    public function getModelClassName(): string
    {
        return static::MODEL;
    }

    public function get(): Collection
    {
        return $this->query()->get();
    }

    public function paginate($perPage): LengthAwarePaginator
    {
        return $this->query->paginate($perPage);
    }

    public function embed($relationship): Builder
    {
        return $this->query()->with($relationship);
    }
}
