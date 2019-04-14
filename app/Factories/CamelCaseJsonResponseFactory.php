<?php


namespace App\Factories;

use Illuminate\Contracts\Support\Arrayable;
use Laravel\Lumen\Http\ResponseFactory as BaseResponseFactory;


class CamelCaseJsonResponseFactory extends BaseResponseFactory
{
    public function json($data = array(), $status = 200, array $headers = array(), $options = 0)
    {
        $json = $this->encodeJson($data);
        return parent::json($json, $status, $headers, $options);
    }

    /**
     * Encode a value to camelCase JSON
     */
    public function encodeJson($value)
    {
        if ($value instanceof Arrayable) {
            return $this->encodeArrayable($value);
        } else if (is_array($value)) {
            return $this->encodeArray($value);
        } else if (is_object($value)) {
            return $this->encodeArray((array) $value);
        } else {
            return $value;
        }
    }

    /**
     * Encode a arrayable
     */
    public function encodeArrayable($arrayable)
    {
        $array = $arrayable->toArray();
        return $this->encodeJson($array);
    }

    /**
     * Encode an array
     */
    public function encodeArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $val) {
            $newArray[$this->toCamelCase($key)] = $this->encodeJson($val);
        }
        return $newArray;
    }

    private function toCamelCase($string)
    {
        return preg_replace_callback(
            '/_([^_])/',
            function (array $m) {
                return ucfirst($m[1]);
            },
            $string
        );
    }
}
