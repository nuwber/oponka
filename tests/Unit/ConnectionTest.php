<?php

namespace Nuwber\Oponka\Tests\Unit;

use Mockery;
use Nuwber\Oponka\Connection;
use Nuwber\Oponka\Tests\TestCase;

class ConnectionTest extends TestCase
{
    private $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = new Connection($this->app);
    }

    /**
     * @test
     */
    public function it_returns_a_map_builder()
    {
        $this->assertInstanceOf(\Nuwber\Oponka\Map\Builder::class, $this->connection->getMapBuilder());
    }

    /**
     * @test
     */
    public function it_sets_the_default_index()
    {
        $this->connection->setDefaultIndex('custom-index');

        $this->assertEquals('custom-index', $this->connection->getDefaultIndex());
    }

    /**
     * @test
     */
    public function it_returns_a_map_grammar()
    {
        $this->assertInstanceOf(\Nuwber\Oponka\Map\Grammar::class, $this->connection->getMapGrammar());
    }

    /**
     * @test
     */
    public function it_returns_a_dsl_query()
    {
        $this->assertInstanceOf(\OpenSearchDSL\Search::class, $this->connection->getDSLQuery());
    }

    /**
     * @test
     */
    public function it_returns_an_elastic_search_client()
    {
        $this->assertInstanceOf('OpenSearch\Client', $this->connection->getClient());
    }

    /**
     * @test
     */
    public function it_returns_the_default_index()
    {
        $this->connection->setDefaultIndex('oponka');

        $this->assertEquals('oponka', $this->connection->getDefaultIndex());
    }

    /**
     * @test
     */
    public function it_executes_a_map_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('indices->putMapping')->withArgs([['index' => 'oponka']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->mapStatement(['index' => 'oponka']));
    }

    /**
     * @test
     */
    public function it_executes_a_search_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('search')->withArgs([['index' => 'oponka']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->searchStatement(['index' => 'oponka']));
    }

    /**
     * @test
     */
    public function it_executes_a_suggest_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('search')->withArgs([['index' => 'oponka']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->suggestStatement(['index' => 'oponka']));
    }

    /**
     * @test
     */
    public function it_executes_a_index_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('index')->withArgs([['index' => 'oponka']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->indexStatement(['index' => 'oponka']));
    }

    /**
     * @test
     */
    public function it_executes_an_update_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('update')->withArgs([['index' => 'oponka']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->updateStatement(['index' => 'oponka']));
    }

    /**
     * @test
     */
    public function it_executes_a_delete_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('delete')->withArgs([['index' => 'oponka']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->deleteStatement(['index' => 'oponka']));
    }

    /**
     * @test
     */
    public function it_executes_an_exists_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('exists')->withArgs([['index' => 'oponka']])->andReturn(true);

        $this->connection->setClient($client);

        $this->assertEquals(true, $this->connection->existsStatement(['index' => 'oponka']));
    }

    /**
     * @test
     */
    public function it_executes_a_bulk_statement_on_client()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('bulk')->withArgs([['test' => 'test']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->bulkStatement(['test' => 'test']));
    }

    /**
     * @test
     */
    public function it_executes_a_statement_with_custom_index()
    {
        $client = Mockery::mock('OpenSearch\Client');
        $client->shouldReceive('update')->withArgs([['index' => 'custom_index']])->andReturn(['ok']);
        $client->shouldReceive('exists')->withArgs([['index' => 'custom_index']])->andReturn(true);
        $client->shouldReceive('delete')->withArgs([['index' => 'custom_index']])->andReturn(['ok']);
        $client->shouldReceive('search')->withArgs([['index' => 'custom_index']])->andReturn(['ok']);
        $client->shouldReceive('indices->putMapping')->withArgs([['index' => 'custom_index']])->andReturn(['ok']);
        $client->shouldReceive('suggest')->withArgs([['index' => 'custom_index']])->andReturn(['ok']);
        $client->shouldReceive('index')->withArgs([['index' => 'custom_index']])->andReturn(['ok']);

        $this->connection->setClient($client);

        $this->assertEquals(['ok'], $this->connection->updateStatement(['index' => 'custom_index']));
        $this->assertTrue($this->connection->existsStatement(['index' => 'custom_index']));
        $this->assertEquals(['ok'], $this->connection->deleteStatement(['index' => 'custom_index']));
        $this->assertEquals(['ok'], $this->connection->searchStatement(['index' => 'custom_index']));
        $this->assertEquals(['ok'], $this->connection->mapStatement(['index' => 'custom_index']));
        $this->assertEquals(['ok'], $this->connection->suggestStatement(['index' => 'custom_index']));
        $this->assertEquals(['ok'], $this->connection->indexStatement(['index' => 'custom_index']));
    }

    /**
     * @test
     */
    public function it_starts_a_fluent_search_query_builder()
    {
        $this->assertInstanceOf('Nuwber\Oponka\DSL\SearchBuilder', $this->connection->search());
    }

    /**
     * @test
     */
    public function it_starts_a_fluent_suggest_query_builder()
    {
        $this->assertInstanceOf('Nuwber\Oponka\DSL\SearchBuilder', $this->connection->suggest());
    }

}
