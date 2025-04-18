# Oponka: Laravel OpenSearch Integration

> This package was inspired by the [Plastic](https://github.com/sleimanx2/plastic) package, which provided a similar integration for Elasticsearch. 
>Oponka builds upon the concepts established by Plastic, adapting them for the OpenSearch ecosystem. However, unlike Plastic, Oponka focuses solely on OpenSearch and does not include functionality for mapping indices to Eloquent models.

Oponka provides a seamless integration with OpenSearch for Laravel applications (version 10 and above). It simplifies the process of interacting with OpenSearch, allowing you to easily perform searches, index documents, and manage your OpenSearch data.

## Features
**Native OpenSearch Library**: Utilizes the official OpenSearch PHP client for direct and efficient communication with your OpenSearch cluster.</br>
**OpenSearch DSL**: Leverages the opensearch-dsl library to provide a more intuitive and expressive syntax for building complex search queries.</br>
**Easy Configuration**: Simple configuration options allow you to quickly connect to your OpenSearch cluster and customize the package's behavior.</br>
**Laravel Integration**: Integrates seamlessly with Laravel's service container and provides convenient helpers for accessing OpenSearch functionality within your application.

## Installation
Install the package using Composer:
```shell
php composer require nuwber/oponka
```

## Configuration
The OponkaServiceProvider will be used automatically as it is configured in the composer.json file.

If you need it in your project, you can use the following commands:
```shell
php artisan vendor:publish --provider="Nuwber\Oponka\OponkaServiceProvider"
```

### Configure the Oponka connection:
Open the config/oponka.php file and change the connection settings as needed. Or do it with `.env` file.

## Usage

The primary way to interact with Oponka is through the `Oponka` facade.

### Getting the Client

You can access the underlying OpenSearch PHP client instance directly if needed:

```php
use Nuwber\Oponka\Facades\Oponka;

$client = Oponka::client(); 
// Now you can use the $client directly with the OpenSearch PHP client methods
```

### Indexing Documents

To index a document, you can use the `index` method:

```php
use Nuwber\Oponka\Facades\Oponka;

$params = [
    'index' => 'my_index',
    'id'    => 'my_id',
    'body'  => ['testField' => 'abc']
];

$response = Oponka::index($params);

// $response contains the OpenSearch response array
```

### Searching Documents

Oponka integrates with `opensearch-dsl/opensearch-dsl`. You can build your queries using its syntax.

```php
use Nuwber\Oponka\Facades\Oponka;
use OpenSearchDSL\Search;
use OpenSearchDSL\Query\Compound\BoolQuery;
use OpenSearchDSL\Query\TermLevel\TermQuery;
use OpenSearchDSL\Query\FullText\MatchQuery;

// Get the DSL Search object
$search = Oponka::search(); // Or potentially a method to get a new Search object

// Build a query
$query = new BoolQuery();
$query->add(new MatchQuery('title', 'laravel'));
$query->add(new TermQuery('status', 'published'), BoolQuery::FILTER);

$search->addQuery($query);
$search->setSource(['title', 'status', 'publish_date']);
$search->setSize(10);
$search->setFrom(0); // for pagination

// Prepare the parameters for OpenSearch
$params = [
    'index' => 'my_index',
    'body' => $search->toArray(),
];

// Execute the search
$results = Oponka::search($params); 

// Process results (assuming a Result object or similar is returned)
// foreach ($results->getHits() as $hit) {
//     // Access hit data: $hit['_source'], $hit['_score'], etc.
// }
// $total = $results->getTotal();
```

*Note: The exact methods for accessing the DSL builder and processing results might differ slightly. Please refer to the source code or tests for precise implementation.*

### Updating Documents

Use the `update` method:

```php
use Nuwber\Oponka\Facades\Oponka;

$params = [
    'index' => 'my_index',
    'id'    => 'my_id',
    'body'  => [
        'doc' => [
            'new_field' => 'xyz'
        ]
    ]
];

$response = Oponka::update($params);
```

### Deleting Documents

Use the `delete` method:

```php
use Nuwber\Oponka\Facades\Oponka;

$params = [
    'index' => 'my_index',
    'id'    => 'my_id'
];

$response = Oponka::delete($params);
```

### Direct Client Access

For operations not directly exposed or for more complex scenarios, you can always fall back to the native OpenSearch client:

```php
use Nuwber\Oponka\Facades\Oponka;

$client = Oponka::client();

$response = $client->cat()->indices(['index' => 'my_index', 'v' => true]); 
```
