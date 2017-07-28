<?php
use Illuminate\Container\Container;

if (! function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string  $key
     * @param  mixed  $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('config');
        }
        if (is_array($key)) {
            return app('config')->set($key);
        }
        return app('config')->get($key, $default);
    }
}

if (! function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param  string  $abstract
     * @param  array   $parameters
     * @return Illuminate\Container\Container
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }
        return empty($parameters)
            ? Container::getInstance()->make($abstract)
            : Container::getInstance()->makeWith($abstract, $parameters);
    }
}

if (! function_exists('inJson')) {
    /**
     * Sugar json_encode
     *
     * @param array $values
     * @return string
     */
    function inJson(array $values = []) : string
    {
      return json_encode($values);
    }
}

if (! function_exists('inJson')) {
    /**
     * Sugar json_encode
     *
     * @param string $json
     * @param bool   $assoc
     * @return string
     */
    function fromJson(string $json = '', bool $assoc = false) : string
    {
        return json_decode($json, $assoc);
    }
}