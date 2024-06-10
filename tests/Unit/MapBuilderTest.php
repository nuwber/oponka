<?php

namespace Nuwber\Oponka\Tests\Unit;

use Mockery;
use Nuwber\Oponka\Tests\TestCase;

class MapBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_a_map_from_blueprint()
    {
        $connection = Mockery::mock('Nuwber\Oponka\Connection')->makePartial();

        $connection->shouldReceive('mapStatement')->once();

        $builder = new \Nuwber\Oponka\Map\Builder($connection);

        $builder->create('posts', function ($blueprint) {
            $blueprint->ip('ip');
        });
    }
}
