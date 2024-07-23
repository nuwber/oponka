<?php

namespace Nuwber\Oponka\Facades;

use Illuminate\Support\Facades\Facade;

class Map extends Facade
{
    /**
     * Get a map builder instance for the default connection.
     *
     * @return \Nuwber\Oponka\Map\Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['oponka']->connection()->getMapBuilder();
    }
}
