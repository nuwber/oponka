<?php

namespace Nuwber\Oponka;


use Illuminate\Contracts\Container\Container;

class SearchManager
{
    /**
     * @var Connection
     */
    protected Connection $connection;

    public function __construct(private readonly Container $app)
    {
    }

    /**
     * Get an OpenSearch search connection instance.
     *
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->app['oponka.connection'];
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters): mixed
    {
        return call_user_func_array([$this->connection(), $method], $parameters);
    }
}
