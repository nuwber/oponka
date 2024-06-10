<?php

namespace Nuwber\Oponka;

use Illuminate\Support\Collection;

class Result
{
    /**
     * Time needed to execute the query.
     *
     * @var int
     */
    protected int $took;

    /**
     * Check if the query timed out.
     *
     * @var bool
     */
    protected bool $timed_out;

    protected array $shards;

    /**
     * Result of the query.
     *
     * @var Collection
     */
    protected Collection $hits;

    /**
     * Total number of hits.
     *
     * @var int
     */
    protected int $totalHits;

    /**
     * Highest document score.
     *
     * @var float
     */
    protected ?float $maxScore;

    /**
     * The aggregations result.
     *
     * @var array|null
     */
    protected ?array $aggregations = null;

    public function __construct(array $results)
    {
        $this->took = $results['took'];
        $this->timed_out = $results['timed_out'];
        $this->shards = $results['_shards'];
        $this->hits = new Collection($results['hits']['hits']);
        $this->totalHits = $results['hits']['total']['value'];
        $this->maxScore = $results['hits']['max_score'];
        $this->aggregations = isset($results['aggregations']) ? $results['aggregations'] : [];
    }

    /**
     * Total Hits.
     *
     * @return int
     */
    public function totalHits(): int
    {
        return $this->totalHits;
    }

    /**
     * Max Score.
     *
     * @return float
     */
    public function maxScore(): float
    {
        return $this->maxScore;
    }

    /**
     * Get Shards.
     */
    public function shards(): array
    {
        return $this->shards;
    }

    /**
     * Took.
     *
     * @return int
     */
    public function took(): int
    {
        return $this->took;
    }

    /**
     * Timed Out.
     *
     * @return bool
     */
    public function timedOut(): bool
    {
        return (bool)$this->timed_out;
    }

    /**
     * Get Hits.
     *
     * Get the hits from Elasticsearch
     * results as a Collection.
     *
     * @return Collection
     */
    public function hits(): Collection
    {
        return $this->hits;
    }

    /**
     * Set the hits value.
     *
     * @param $values
     */
    public function setHits(Collection $values): void
    {
        $this->hits = $values;
    }

    /**
     * Get aggregations.
     *
     * Get the raw hits array from
     * Elasticsearch results.
     *
     * @return array|null
     */
    public function aggregations(): ?array
    {
        return $this->aggregations;
    }
}
