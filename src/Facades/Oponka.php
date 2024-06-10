<?php

namespace Nuwber\Oponka\Facades;

use Illuminate\Support\Facades\Facade;
use Nuwber\Oponka\Connection;
use Nuwber\Oponka\SearchManager;
use OpenSearch\Client;
use OpenSearch\Namespaces\IndicesNamespace;

/**
 * @method static IndicesNamespace indices()
 * @method static Client getClient()
 * @method static Connection connection()
 * @method static void bulkStatement(array $params)
 * @method static string getDefaultIndex()
 *
 * @mixin SearchManager
 * @see \OpenSearch\Namespaces\IndicesNamespace::indices
 */
class Oponka extends Facade
{
    /**
     * Get a oponka manager instance for the default connection.
     *
     * @return SearchManager|string
     */
    protected static function getFacadeAccessor()
    {
        return 'oponka';
    }
}
