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
