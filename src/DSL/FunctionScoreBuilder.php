<?php

namespace Nuwber\Oponka\DSL;

use OpenSearchDSL\BuilderInterface;
use OpenSearchDSL\Query\Compound\FunctionScoreQuery;

class FunctionScoreBuilder
{
    /**
     * @var FunctionScoreQuery
     */
    private readonly FunctionScoreQuery $query;

    /**
     * FunctionScoreBuilder constructor.
     *
     * @param SearchBuilder $search
     * @param array|null $parameters
     */
    public function __construct(SearchBuilder $search, ?array $parameters = [])
    {
        $this->query = new FunctionScoreQuery($search->query->getQueries(), $parameters);
    }

    /**
     * @param string $field
     * @param float $factor
     * @param string $modifier
     * @param BuilderInterface|null $query
     */
    public function field(
        string $field,
        float $factor,
        string $modifier = 'none',
        ?BuilderInterface $query = null
    ): void {
        $this->query->addFieldValueFactorFunction($field, $factor, $modifier, $query);
    }

    /**
     * @param string $type
     * @param string $field
     * @param array $function
     * @param array $options
     * @param BuilderInterface|null $query
     */
    public function decay(
        string $type,
        string $field,
        array $function,
        array $options = [],
        ?BuilderInterface $query = null
    ): void {
        $this->query->addDecayFunction($type, $field, $function, $options, $query);
    }

    /**
     * @param float $weight
     * @param BuilderInterface|null $query
     */
    public function weight(float $weight, ?BuilderInterface $query = null): void
    {
        $this->query->addWeightFunction($weight, $query);
    }

    /**
     * @param int|null $seed
     * @param BuilderInterface|null $query
     */
    public function random(?int $seed = null, ?BuilderInterface $query = null): void
    {
        $this->query->addRandomFunction($seed, $query);
    }

    /**
     * @param string $inline
     * @param array $params
     * @param array $options
     * @param BuilderInterface|null $query
     */
    public function script(
        string $inline,
        array $params = [],
        array $options = [],
        ?BuilderInterface $query = null
    ): void {
        $this->query->addScriptScoreFunction($inline, $params, $options, $query);
    }

    /**
     * @param array $functions
     */
    public function simple(array $functions): void
    {
        $this->query->addSimpleFunction($functions);
    }

    /**
     * Return the DSL query.
     *
     * @return array
     */
    public function toDSL(): array
    {
        return $this->query->toArray();
    }

    /**
     * @return FunctionScoreQuery
     */
    public function getQuery(): FunctionScoreQuery
    {
        return $this->query;
    }
}
