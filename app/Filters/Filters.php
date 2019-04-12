<?php


namespace App\Filters;


use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Filter
{
    public function __construct(Request $request = null)
    {
        if ($request && is_array($request->get('embeds'))) {
            $this->applyEmbeds($request->get('embeds'));
        }
        if ($request && is_array($request->get('filters'))) {
            $this->applyFilters($request->get('filters'));
        }

        if (method_exists($this, 'defaultFilters')) {
            return $this->defaultFilters();
        }
    }

    /**
     * @param array $filters
     *
     * @return $this
     */
    public function applyFilters(array $filters): Filter
    {
        foreach ($filters as $method => $value) {
            if (!$value) {
                continue;
            }
            $this->callFilterMethod(Str::camel($method), $value);
        }

        return $this;
    }

    /**
     * @param array $filters
     *
     * @return $this
     */
    public function applyEmbeds(array $embeds): Filter
    {
        foreach ($embeds as $value) {
            if (!$value) {
                continue;
            }
            $this->callEmbedMethod($value);
        }

        return $this;
    }

    protected function callFilterMethod(string $method, $value)
    {
        $method = 'filter' . ucfirst($method);
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
    }

    protected function callEmbedMethod(string $value)
    {
        $method = 'embed';
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
    }
}
