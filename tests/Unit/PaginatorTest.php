<?php

namespace Nuwber\Oponka\Tests\Unit;

use Nuwber\Oponka\Tests\TestCase;

class PaginatorTest extends TestCase
{
    protected $result = [
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
    public function it_has_access_to_the_given_result()
    {
        $result = new \Nuwber\Oponka\Result($this->result);
        $paginator = new \Nuwber\Oponka\Paginator($result, 1, 1);
        $this->assertEquals($result, $paginator->result());
    }
}
