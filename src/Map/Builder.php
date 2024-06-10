<?php

namespace Nuwber\Oponka\Map;

use Closure;
use Nuwber\Oponka\Connection;

class Builder
{
    /**
     * Map grammar instance.
     */
    protected Grammar $grammar;

    /**
     * Blueprint resolver callback.
     *
     * @var Closure
     */
    protected $resolver;

    /**
     * Schema constructor.
     *
     * @param Connection $connection
     */
    public function __construct(protected Connection $connection)
    {
        $this->grammar = $connection->getMapGrammar();
    }

    /**
     * Create a map on your OpenSearch index.
     *
     * @param string $type
     * @param Closure $callback
     * @param string|null $index
     */
    public function create(string $type, Closure $callback, ?string $index = null): void
    {
        $blueprint = $this->createBlueprint($type, $closure = null, $index);

        $blueprint->create();

        $callback($blueprint);

        $this->build($blueprint);
    }

    /**
     * Execute the blueprint to build.
     *
     * @param Blueprint $blueprint
     */
    protected function build(Blueprint $blueprint): void
    {
        $blueprint->build($this->connection, $this->grammar);
    }

    /**
     * Create a new command set with a Closure.
     *
     * @param string $type
     * @param Closure|null $callback
     * @param null         $index
     *
     * @return mixed|Blueprint
     */
    protected function createBlueprint(string $type, Closure $callback = null, $index = null): mixed
    {
        if (isset($this->resolver)) {
            return call_user_func($this->resolver, $type, $callback, $index);
        }

        return new Blueprint($type, $callback, $index);
    }

    /**
     * Set the Schema Blueprint resolver callback.
     *
     * @param \Closure $resolver
     *
     * @return void
     */
    public function blueprintResolver(Closure $resolver): void
    {
        $this->resolver = $resolver;
    }
}
