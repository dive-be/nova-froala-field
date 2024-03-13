<?php declare(strict_types=1);

namespace Froala\Nova;

use Illuminate\Support\AggregateServiceProvider;
use Laravel\Nova\Nova;
use NovaKit\NovaPackagesTool\LaravelServiceProvider;

final class FroalaServiceProvider extends AggregateServiceProvider
{
    public const string NAME = 'froala';

    protected $providers = [LaravelServiceProvider::class];

    private string $name = self::NAME;

    public function boot(): void
    {
        $this->registerAssets();
        $this->registerMigrations();
        $this->registerPublishables();
        $this->registerRoutes();
    }

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__ . "/../config/{$this->name}.php", self::NAME);
    }

    private function registerAssets(): void
    {
        Nova::serving(static function () {
            Nova::script(Froala::NAME, __DIR__ . '/../dist/js/field.js');
            Nova::style(Froala::NAME, __DIR__ . '/../dist/css/field.css');
        });
    }

    private function registerMigrations(): void
    {
        if ($this->app->runningInConsole() && Froala::$runsMigrations) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    private function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . "/../dist/css/{$this->name}_styles.min.css" => $this->app->publicPath("css/vendor/{$this->name}_styles.min.css"),
            ], "{$this->name}-styles");

            $this->publishes([
                __DIR__ . "/../config/{$this->name}.php" => $this->app->configPath("{$this->name}.php"),
            ], "{$this->name}-config");
        }
    }

    private function registerRoutes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $this->app['router']->group([
            'middleware' => 'nova',
            'prefix' => "nova-vendor/{$this->name}",
        ], __DIR__ . '/../routes/api.php');
    }
}
