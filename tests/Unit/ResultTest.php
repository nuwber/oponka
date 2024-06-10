<?php

namespace Nuwber\Oponka\Tests\Unit;

use Nuwber\Oponka\Result;
use Nuwber\Oponka\Tests\TestCase;

class ResultTest extends TestCase
{
    protected array $result = [
        'took' => 0.2,
        'timed_out' => false,
        '_shards' => [
            'total' => 1,
            'successful' => 1,
            'skipped' => 0,
            'failed' => 0,
        ],
        'hits' => [
            'total' => ['value' => 2],
            'max_score' => 3,
            'hits' => ['foo', 'bar'],

        ],
        'aggregations' => ['aggregations'],
    ];

    /**
     * @test
     */
    public function it_gets_the_number_of_total_hits()
    {
        $result = new Result($this->result);
        $this->assertEquals(2, $result->totalHits());
    }

    /**
     * @test
     */
    public function it_gets_the_maxScore()
    {
        $result = new Result($this->result);
        $this->assertEquals(3, $result->maxScore());
    }

    /**
     * @test
     */
    public function it_gets_the_hits()
    {
        $result = new Result($this->result);
        $this->assertEquals(['foo', 'bar'], $result->hits()->all());
    }

    /**
     * @test
     */
    public function it_gets_if_the_query_timed_out()
    {
        $result = new Result($this->result);
        $this->assertEquals(false, $result->timedOut());
    }

    /**
     * @test
     */
    public function it_gets_the_query_aggregations()
    {
        $result = new Result($this->result);
        $this->assertEquals(['aggregations'], $result->aggregations());
    }

    /**
     * @test
     */
    public function it_gets_the_query_shards()
    {
        $result = new Result($this->result);

        $this->assertEquals([
            'total' => 1,
            'successful' => 1,
            'skipped' => 0,
            'failed' => 0,
        ], $result->shards());
    }

    /**
     * @test
     */
    public function it_sets_if_the_query_hits()
    {
        $result = new Result($this->result);
        $result->setHits(collect(['baz']));
        $this->assertEquals(['baz'], $result->hits()->toArray());
    }
}
