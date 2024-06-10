<?php

namespace Nuwber\Oponka;

use Illuminate\Contracts\Container\Container;
use OpenSearch\Client;
use OpenSearch\ClientBuilder;
use OpenSearch\Namespaces\IndicesNamespace;
use OpenSearchDSL\Search as DSLQuery;
use Nuwber\Oponka\DSL\SearchBuilder;
use Nuwber\Oponka\Map\Builder as MapBuilder;
use Nuwber\Oponka\Map\Grammar as MapGrammar;

class Connection
{
    /**
     * OpenSearch default index.
     *
     * @var string
     */
    protected $index;

    /**
     * Elasticsearch client instance.
     *
     * @var Client
     */
    protected Client $client;

    public function __construct(private readonly Container $app)
    {
        $this->client = $this->buildClient($app['config']['oponka.connection']);
    }

    public function getNew(): static
    {
        return new Connection($this->app);
    }

    /**
     * Get the default OpenSearch index.
     *
     * @return string
     */
    public function getDefaultIndex(): string
    {
        return $this->index;
    }

    /**
     * Get map builder instance for this connection.
     *
     * @return MapBuilder
     */
    public function getMapBuilder(): MapBuilder
    {
        return new MapBuilder($this);
    }

    /**
     * Get map grammar instance for this connection.
     *
     * @return MapGrammar
     */
    public function getMapGrammar(): MapGrammar
    {
        return new MapGrammar();
    }

    /**
     * Get DSL grammar instance for this connection.
     *
     * @return DSLQuery
     */
    public function getDSLQuery(): DSLQuery
    {
        return new DSLQuery();
    }

    /**
     * Get the OpenSearch search client instance.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Set a custom OpenSearch client.
     *
     * @param Client $client
     * @return Connection
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Set the default index.
     *
     * @param string $index
     *
     * @return Connection
     */
    public function setDefaultIndex(string $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function indices(): IndicesNamespace
    {
        return $this->client->indices();
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $mappings
     *
     * @return array
     */
    public function mapStatement(array $mappings): array
    {
        return $this->client->indices()->putMapping($this->setStatementIndex($mappings));
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $search
     *
     * @return array
     */
    public function searchStatement(array $search): array
    {
        return $this->client->search($this->setStatementIndex($search));
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $suggestions
     *
     * @return array
     */
    public function suggestStatement(array $suggestions): array
    {
        return $this->client->search($this->setStatementIndex($suggestions));
    }

    /**
     * Execute a insert statement on index;.
     *
     * @param array $params
     *
     * @return array
     */
    public function indexStatement(array $params): array
    {
        return $this->client->index($this->setStatementIndex($params));
    }

    /**
     * Execute a update statement on index;.
     *
     * @param array $params
     *
     * @return array
     */
    public function updateStatement(array $params): array
    {
        return $this->client->update($this->setStatementIndex($params));
    }

    /**
     * Execute a update statement on index;.
     *
     * @param array $params
     *
     * @return array
     */
    public function deleteStatement(array $params): array
    {
        return $this->client->delete($this->setStatementIndex($params));
    }

    /**
     * Execute a exists statement on index.
     *
     * @param array $params
     *
     * @return array|bool
     */
    public function existsStatement(array $params): array|bool
    {
        return $this->client->exists($this->setStatementIndex($params));
    }

    /**
     * Execute a bulk statement on index;.
     *
     * @param array $params
     *
     * @return array
     */
    public function bulkStatement(array $params): array
    {
        return $this->client->bulk($params);
    }

    /**
     * Begin a fluent search query builder.
     *
     * @return SearchBuilder
     */
    public function search(): SearchBuilder
    {
        return new SearchBuilder($this, $this->getDSLQuery());
    }

    /**
     * Begin a fluent suggest query builder.
     *
     * @return SearchBuilder
     */
    public function suggest(): SearchBuilder
    {
        return new SearchBuilder($this, $this->getDSLQuery());
    }

    /**
     * Create an OpenSearch search instance.
     *
     * @param array $config
     *
     * @return Client
     */
    private function buildClient(array $config): Client
    {
        $client = ClientBuilder::create()
            ->setHosts($config['hosts']);

        if (isset($config['retries'])) {
            $client->setRetries($config['retries']);
        }

        if (isset($config['logging']) and $config['logging']['enabled']) {
            $client->setLogger($this->app['logger']);
        }

        return $client->build();
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function setStatementIndex(array $params): array
    {
        if (isset($params['index']) and $params['index']) {
            return $params;
        }

        // merge the default index with the given params if the index is not set.
        return array_merge($params, ['index' => $this->getDefaultIndex()]);
    }
}
