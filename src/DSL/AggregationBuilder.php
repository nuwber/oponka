<?php

namespace Nuwber\Oponka\DSL;

use OpenSearchDSL\Aggregation\AbstractAggregation;
use OpenSearchDSL\Aggregation\Bucketing\DateRangeAggregation;
use OpenSearchDSL\Aggregation\Bucketing\GeoDistanceAggregation;
use OpenSearchDSL\Aggregation\Bucketing\GeoHashGridAggregation;
use OpenSearchDSL\Aggregation\Bucketing\HistogramAggregation;
use OpenSearchDSL\Aggregation\Bucketing\Ipv4RangeAggregation;
use OpenSearchDSL\Aggregation\Bucketing\MissingAggregation;
use OpenSearchDSL\Aggregation\Bucketing\RangeAggregation;
use OpenSearchDSL\Aggregation\Bucketing\TermsAggregation;
use OpenSearchDSL\Aggregation\Metric\AvgAggregation;
use OpenSearchDSL\Aggregation\Metric\CardinalityAggregation;
use OpenSearchDSL\Aggregation\Metric\GeoBoundsAggregation;
use OpenSearchDSL\Aggregation\Metric\MaxAggregation;
use OpenSearchDSL\Aggregation\Metric\MinAggregation;
use OpenSearchDSL\Aggregation\Metric\PercentileRanksAggregation;
use OpenSearchDSL\Aggregation\Metric\PercentilesAggregation;
use OpenSearchDSL\Aggregation\Metric\StatsAggregation;
use OpenSearchDSL\Aggregation\Metric\SumAggregation;
use OpenSearchDSL\Aggregation\Metric\ValueCountAggregation;
use OpenSearchDSL\Search as Query;

class AggregationBuilder
{
    public function __construct(private readonly ?Query $query = null)
    {
    }

    /**
     * Add an average aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function average(string $alias, ?string $field = null, ?string $script = null): void
    {
        $this->append(new AvgAggregation($alias, $field, $script));
    }

    /**
     * Add an cardinality aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     * @param int|null $precision
     * @param bool $rehash
     */
    public function cardinality(
        string $alias,
        ?string $field = null,
        ?string $script = null,
        ?int $precision = null,
        ?bool $rehash = null
    ): void {
        $aggregation = (new CardinalityAggregation($alias))
            ->setField($field)
            ->setScript($script)
            ->setPrecisionThreshold($precision)
            ->setRehash($rehash);

        $this->append($aggregation);
    }

    /**
     * Add a date range aggregate.
     *
     * @param string $alias
     * @param string $field
     * @param string $format
     * @param array $ranges
     *
     * @internal param null $from
     * @internal param null $to
     */
    public function dateRange(string $alias, string $field, string $format, array $ranges): void
    {
        $this->append(new DateRangeAggregation($alias, $field, $format, $ranges));
    }

    /**
     * Add a geo bounds aggregate.
     *
     * @param string $alias
     * @param null|string $field
     * @param bool $wrap_longitude
     */
    public function geoBounds(string $alias, ?string $field, bool $wrap_longitude = true): void
    {
        $this->append(new GeoBoundsAggregation($alias, $field, $wrap_longitude));
    }

    /**
     * Add a geo bounds aggregate.
     *
     * @param string $alias
     * @param null|string $field
     * @param string $origin
     * @param array $ranges
     */
    public function geoDistance(string $alias, ?string $field, string $origin, array $ranges): void
    {
        $this->append(new GeoDistanceAggregation($alias, $field, $origin, $ranges));
    }

    /**
     * Add a geo hash grid aggregate.
     *
     * @param string $alias
     * @param null|string $field
     * @param float $precision
     * @param int|null $size
     * @param int|null $shardSize
     */
    public function geoHashGrid(
        string $alias,
        ?string $field,
        ?int $precision,
        ?int $size = null,
        ?int $shardSize = null
    ): void {
        $this->append(new GeoHashGridAggregation($alias, $field, $precision, $size, $shardSize));
    }

    /**
     * Add a histogram aggregate.
     *
     * @param string $alias
     * @param string $field
     * @param int $interval
     * @param int|null $minDocCount
     * @param string|null $orderMode
     * @param string $orderDirection
     * @param int|null $extendedBoundsMin
     * @param int|null $extendedBoundsMax
     * @param bool $keyed
     */
    public function histogram(
        string $alias,
        string $field,
        int $interval,
        ?int $minDocCount = null,
        ?string $orderMode = null,
        string $orderDirection = 'asc',
        ?int $extendedBoundsMin = null,
        ?int $extendedBoundsMax = null,
        ?bool $keyed = null
    ): void {
        $aggregation = new HistogramAggregation(
            $alias,
            $field,
            $interval,
            $minDocCount,
            $orderMode,
            $orderDirection,
            $extendedBoundsMin,
            $extendedBoundsMax,
            $keyed
        );

        $this->append($aggregation);
    }

    /**
     * Add an ipv4 range aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param array $ranges
     */
    public function ipv4Range(string $alias, ?string $field, array $ranges): void
    {
        $this->append(new Ipv4RangeAggregation($alias, $field, $ranges));
    }

    /**
     * Add an max aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function max(string $alias, ?string $field = null, ?string $script = null): void
    {
        $this->append(new MaxAggregation($alias, $field, $script));
    }

    /**
     * Add an min aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function min(string $alias, ?string $field = null, ?string $script = null): void
    {
        $this->append(new MinAggregation($alias, $field, $script));
    }

    /**
     * Add an missing aggregate.
     *
     * @param string $alias
     * @param string $field
     */
    public function missing(string $alias, string $field): void
    {
        $this->append(new MissingAggregation($alias, $field));
    }

    /**
     * Add an percentile aggregate.
     *
     * @param string $alias
     * @param string $field
     * @param $percents
     * @param string|null $script
     */
    public function percentile(
        string $alias,
        string $field,
        ?array $percents = null,
        ?string $script = null
    ): void {
        $this->append(new PercentilesAggregation($alias, $field, $percents, $script));
    }

    /**
     * Add an percentileRanks aggregate.
     *
     * @param string $alias
     * @param string $field
     * @param array $values
     * @param string|null $script
     */
    public function percentileRanks(
        string $alias,
        string $field,
        array $values,
        ?string $script = null
    ): void {
        $this->append(new PercentileRanksAggregation($alias, $field, $values, $script));
    }

    /**
     * Add an stats aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function stats(string $alias, ?string $field = null, ?string $script = null): void
    {
        $this->append(new StatsAggregation($alias, $field, $script));
    }

    /**
     * Add an sum aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function sum(string $alias, ?string $field = null, ?string $script = null): void
    {
        $this->append(new SumAggregation($alias, $field, $script));
    }

    /**
     * Add a value count aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function valueCount(string $alias, ?string $field = null, ?string $script = null): void
    {
        $this->append(new ValueCountAggregation($alias, $field, $script));
    }

    /**
     * Add a range aggregate.
     *
     * @param string $alias
     * @param string $field
     * @param array $ranges
     * @param bool $keyed
     */
    public function range(string $alias, string $field, array $ranges, bool $keyed = false): void
    {
        $this->append(new RangeAggregation($alias, $field, $ranges, $keyed));
    }

    /**
     * Add a terms aggregate.
     *
     * @param string $alias
     * @param string|null $field
     * @param string|null $script
     */
    public function terms(string $alias, ?string $field = null, ?string $script = null): void
    {
        $this->append(new TermsAggregation($alias, $field, $script));
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
     * Append an aggregation to the aggregation query builder.
     *
     * @param AbstractAggregation $aggregation
     */
    public function append(AbstractAggregation $aggregation): void
    {
        $this->query->addAggregation($aggregation);
    }
}
