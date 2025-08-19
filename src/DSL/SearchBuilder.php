<?php

namespace Nuwber\Oponka\DSL;

use Illuminate\Support\Traits\Macroable;
use Nuwber\Oponka\Connection;
use Nuwber\Oponka\Paginator;
use Nuwber\Oponka\Result;
use OpenSearchDSL\BuilderInterface;
use OpenSearchDSL\Highlight\Highlight;
use OpenSearchDSL\Query\Compound\BoolQuery;
use OpenSearchDSL\Query\FullText\CommonTermsQuery;
use OpenSearchDSL\Query\FullText\MatchQuery;
use OpenSearchDSL\Query\FullText\MultiMatchQuery;
use OpenSearchDSL\Query\FullText\QueryStringQuery;
use OpenSearchDSL\Query\FullText\SimpleQueryStringQuery;
use OpenSearchDSL\Query\Geo\GeoBoundingBoxQuery;
use OpenSearchDSL\Query\Geo\GeoDistanceQuery;
use OpenSearchDSL\Query\Geo\GeoPolygonQuery;
use OpenSearchDSL\Query\Geo\GeoShapeQuery;
use OpenSearchDSL\Query\Joining\NestedQuery;
use OpenSearchDSL\Query\MatchAllQuery;
use OpenSearchDSL\Query\TermLevel\ExistsQuery;
use OpenSearchDSL\Query\TermLevel\FuzzyQuery;
use OpenSearchDSL\Query\TermLevel\IdsQuery;
use OpenSearchDSL\Query\TermLevel\PrefixQuery;
use OpenSearchDSL\Query\TermLevel\RangeQuery;
use OpenSearchDSL\Query\TermLevel\RegexpQuery;
use OpenSearchDSL\Query\TermLevel\TermQuery;
use OpenSearchDSL\Query\TermLevel\TermsQuery;
use OpenSearchDSL\Query\TermLevel\WildcardQuery;
use OpenSearchDSL\Search as Query;
use OpenSearchDSL\Sort\FieldSort;
use OpenSearchDSL\Type\Location;

class SearchBuilder
{
    use Macroable;

    /**
     * An instance of DSL query.
     *
     * @var Query
     */
    public Query $query;

    /**
     * The OpenSearch type to query against.
     *
     * @var string
     */
    public $type;

    /**
     * The OpenSearch index to query against.
     *
     * @var string
     */
    public $index;

    /**
     * Query bool state.
     *
     * @var string
     */
    protected string $boolState = BoolQuery::MUST;

    /**
     * Builder constructor.
     *
     * @param Connection $connection
     * @param Query|null $grammar
     */
    public function __construct(protected Connection $connection, ?Query $grammar = null)
    {
        $this->query = $grammar ?: $connection->getDSLQuery();
    }

    /**
     * Set the OpenSearch type to query against.
     *
     * @param string $type
     *
     * @return $this
     */
    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the OpenSearch index to query against.
     *
     * @param string $index
     *
     * @return $this
     */
    public function index(string $index): static
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Set the query from/offset value.
     *
     * @param int $offset
     *
     * @return $this
     */
    public function from(int $offset): SearchBuilder
    {
        $this->query->setFrom($offset);

        return $this;
    }

    /**
     * Set the query limit/size value.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function size(int $limit): SearchBuilder
    {
        $this->query->setSize($limit);

        return $this;
    }

    /**
     * Set the query sort values values.
     *
     * @param array|string $fields
     * @param string|null $order
     * @param BuilderInterface|null $parameters
     *
     * @return $this
     */
    public function sortBy(array|string $fields, ?string $order = null, ?BuilderInterface $parameters = null): static
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            $this->query->addSort(new FieldSort($field, $order, $parameters));
        }

        return $this;
    }

    /**
     * Set the query min score value.
     *
     * @param int $score
     *
     * @return $this
     */
    public function minScore(int $score): static
    {
        $this->query->setMinScore($score);

        return $this;
    }

    /**
     * Switch to a should statement.
     */
    public function should(): static
    {
        $this->boolState = BoolQuery::SHOULD;

        return $this;
    }

    /**
     * Switch to a must statement.
     */
    public function must(): static
    {
        $this->boolState = BoolQuery::MUST;

        return $this;
    }

    /**
     * Switch to a must not statement.
     */
    public function mustNot(): static
    {
        $this->boolState = BoolQuery::MUST_NOT;

        return $this;
    }

    /**
     * Switch to a filter query.
     */
    public function filter(): static
    {
        $this->boolState = BoolQuery::FILTER;

        return $this;
    }

    /**
     * Add an ids query.
     *
     * @param array | string $ids
     *
     * @return $this
     */
    public function ids(array|string $ids): static
    {
        $ids = is_array($ids) ? $ids : [$ids];

        return $this->append(new IdsQuery($ids));
    }

    /**
     * Add a term query.
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     *
     * @return $this
     */
    public function term(string $field, mixed $term, array $attributes = []): static
    {
        return $this->append(new TermQuery($field, $term, $attributes));
    }

    /**
     * Add an terms query.
     *
     * @param string $field
     * @param array $terms
     * @param array $attributes
     *
     * @return $this
     */
    public function terms(string $field, array $terms, array $attributes = []): static
    {
        return $this->append(new TermsQuery($field, $terms, $attributes));
    }

    /**
     * Add an exists query.
     *
     * @param string|array $fields
     *
     * @return $this
     */
    public function exists(string|array $fields): static
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            $this->append(new ExistsQuery($field));
        }

        return $this;
    }

    /**
     * Add a wildcard query.
     *
     * @param string $field
     * @param string $value
     * @param float $boost
     *
     * @return $this
     */
    public function wildcard(string $field, string $value, float $boost = 1.0): static
    {
        return $this->append(new WildcardQuery($field, $value, ['boost' => $boost]));
    }

    /**
     * Add a boost query.
     *
     * @param float|null $boost
     *
     * @return $this
     *
     * @internal param $field
     */
    public function matchAll(?float $boost = 1.0): static
    {
        return $this->append(new MatchAllQuery(['boost' => $boost]));
    }

    /**
     * Add a match query.
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     *
     * @return $this
     */
    public function match(string $field, string $term, array $attributes = []): static
    {
        return $this->append(new MatchQuery($field, $term, $attributes));
    }

    /**
     * Add a multi match query.
     *
     * @param array $fields
     * @param string $term
     * @param array $attributes
     *
     * @return $this
     */
    public function multiMatch(array $fields, string $term, array $attributes = []): static
    {
        return $this->append(new MultiMatchQuery($fields, $term, $attributes));
    }

    /**
     * Add a geo bounding box query.
     *
     * @param string $field
     * @param array $values
     * @param array $parameters
     *
     * @return $this
     */
    public function geoBoundingBox(string $field, array $values, array $parameters = []): static
    {
        return $this->append(new GeoBoundingBoxQuery($field, $values, $parameters));
    }

    /**
     * Add a geo distance query.
     *
     * @param string $field
     * @param string $distance
     * @param Location $location
     * @param array $attributes
     *
     * @return $this
     */
    public function geoDistance(string $field, string $distance, Location $location, array $attributes = []): static
    {
        return $this->append(new GeoDistanceQuery($field, $distance, $location, $attributes));
    }

    /**
     * Add a geo polygon query.
     *
     * @param string $field
     * @param array $points
     * @param array $attributes
     *
     * @return $this
     */
    public function geoPolygon(string $field, array $points = [], array $attributes = []): static
    {
        return $this->append(new GeoPolygonQuery($field, $points, $attributes));
    }

    /**
     * Add a geo shape query.
     *
     * @param string $field
     * @param string $type
     * @param array $coordinates
     *
     * @return $this
     */
    public function geoShape(string $field, string $type, array $coordinates = []): static
    {
        $query = new GeoShapeQuery();

        $query->addShape($field, $type, $coordinates);

        return $this->append($query);
    }

    /**
     * Add a prefix query.
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     *
     * @return $this
     */
    public function prefix(string $field, string $term, array $attributes = []): static
    {
        return $this->append(new PrefixQuery($field, $term, $attributes));
    }

    /**
     * Add a query string query.
     *
     * @param string $query
     * @param array $attributes
     *
     * @return $this
     */
    public function queryString(string $query, array $attributes = []): static
    {
        return $this->append(new QueryStringQuery($query, $attributes));
    }

    /**
     * Add a simple query string query.
     *
     * @param string $query
     * @param array $attributes
     *
     * @return $this
     */
    public function simpleQueryString(string $query, array $attributes = []): static
    {
        return $this->append(new SimpleQueryStringQuery($query, $attributes));
    }

    /**
     * Add a highlight to result.
     *
     * @param array $fields
     * @param array $parameters
     * @param string $preTag
     * @param string $postTag
     *
     * @return $this
     * @see https://www.elastic.co/guide/en/OpenSearchsearch/reference/current/search-request-highlighting.html
     *
     */
    public function highlight(
        array $fields = ['_all' => []],
        array $parameters = [],
        string $preTag = '<mark>',
        string $postTag = '</mark>'
    ): static {
        $highlight = new Highlight();
        $highlight->setTags([$preTag], [$postTag]);

        foreach ($fields as $field => $fieldParams) {
            $highlight->addField($field, $fieldParams);
        }

        if ($parameters) {
            $highlight->setParameters($parameters);
        }

        $this->query->addHighlight($highlight);

        return $this;
    }

    /**
     * Add a range query.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return $this
     */
    public function range(string $field, array $attributes = []): static
    {
        return $this->append(new RangeQuery($field, $attributes));
    }

    /**
     * Add a regexp query.
     *
     * @param string $field
     * @param string $regex
     * @param array $attributes
     *
     * @return $this
     */
    public function regexp(string $field, string $regex, array $attributes = []): static
    {
        return $this->append(new RegexpQuery($field, $regex, $attributes));
    }

    /**
     * Add a common term query.
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     *
     * @return $this
     */
    public function commonTerm(string $field, string $term, array $attributes = []): static
    {
        return $this->append(new CommonTermsQuery($field, $term, $attributes));
    }

    /**
     * Add a fuzzy query.
     *
     * @param $field
     * @param $term
     * @param array $attributes
     *
     * @return $this
     */
    public function fuzzy($field, $term, array $attributes = []): static
    {
        return $this->append(new FuzzyQuery($field, $term, $attributes));
    }

    /**
     * Add a nested query.
     *
     * @param string $field
     * @param \Closure $closure
     * @param string|null $scoreMode
     *
     * @return $this
     */
    public function nested(string $field, \Closure $closure, ?string $scoreMode = 'avg'): static
    {
        $builder = new SearchBuilder($this->connection, new $this->query());

        $closure($builder);

        $nestedQuery = $builder->query->getQueries();

        return $this->append(new NestedQuery($field, $nestedQuery, ['score_mode' => $scoreMode]));
    }

    /**
     * Add aggregation.
     *
     * @param \Closure $closure
     *
     * @return $this
     */
    public function aggregate(\Closure $closure): static
    {
        $closure(new AggregationBuilder($this->query));

        return $this;
    }

    /**
     * Add function score.
     *
     * @param \Closure $search
     * @param \Closure $closure
     * @param array $parameters
     *
     * @return $this
     */
    public function functions(\Closure $search, \Closure $closure, array $parameters = []): static
    {
        $builder = new self($this->connection, new $this->query());
        $search($builder);

        $builder = new FunctionScoreBuilder($builder, $parameters);

        $closure($builder);

        $this->append($builder->getQuery());

        return $this;
    }

    /**
     * Execute the search query against OpenSearch and return the raw result.
     *
     * @return array
     */
    public function getRaw(): array
    {
        $params = [
            'index' => $this->getIndex(),
            'body' => $this->toDSL(),
        ];

        return $this->connection->searchStatement($params);
    }

    /**
     * Execute the search query against OpenSearch and return the raw result if the model is not set.
     *
     * @return Result
     */
    public function get(): Result
    {
        return new Result($this->getRaw());
    }

    /**
     * Return the current OpenSearch type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Return the current OpenSearch index.
     *
     * @return string|null
     */
    public function getIndex(): ?string
    {
        return $this->index;
    }

    /**
     * Return the current oponka connection.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Return the boolean query state.
     *
     * @return string
     */
    public function getBoolState(): string
    {
        return $this->boolState;
    }

    /**
     * Paginate result hits.
     *
     * @param int $limit
     * @param null|int $current
     *
     * @return Paginator
     */
    public function paginate(int $limit = 25, ?int $current = null): Paginator
    {
        $page = $this->getCurrentPage($current);

        $from = $limit * ($page - 1);
        $size = $limit;

        $result = $this->from($from)->size($size)->get();

        return new Paginator($result, $size, $page);
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
     * Append a query.
     *
     * @param $query
     *
     * @return $this
     */
    public function append($query): static
    {
        $this->query->addQuery($query, $this->getBoolState());

        return $this;
    }

    /**
     * return the current query string value.
     *
     * @param null|int $current
     *
     * @return int
     */
    protected function getCurrentPage(?int $current): int
    {
        return $current ?: (int)request()->get('page', 1);
    }
}
