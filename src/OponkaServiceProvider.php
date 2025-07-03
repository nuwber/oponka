<?php

namespace Nuwber\Oponka;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Nuwber\Oponka\Facades\Oponka;

/**
 * @codeCoverageIgnore
 */
class OponkaServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/oponka.php' => $this->app->configPath('oponka.php'),
            ], 'oponka-config');
        }
    }

    /**
     * Register the service provider.
     */
    #[\Override]
    public function register()
    {
        $this->configure();

        $this->app->singleton('oponka', fn($app) => new SearchManager($app));

        $this->app->singleton('oponka.connection', fn($app) => new Connection($app));

        AliasLoader::getInstance()->alias('Oponka', Oponka::class);
    }

    /**
     * Setup the configuration for Oponka.
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/oponka.php',
            'oponka'
        );
    }
}
