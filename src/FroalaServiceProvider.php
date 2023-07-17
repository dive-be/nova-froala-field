<?php declare(strict_types=1);

namespace Froala\Nova;

use Illuminate\Support\AggregateServiceProvider;
use Laravel\Nova\Nova;
use NovaKit\NovaPackagesTool\LaravelServiceProvider;

final class FroalaServiceProvider extends AggregateServiceProvider
{
    protected $providers = [LaravelServiceProvider::class];

    public function boot(): void
    {
        $this->app->booted($this->routes(...));

        Nova::serving(static function () {
            Nova::script('nova-froala-field', __DIR__ . '/../dist/js/field.js');
            Nova::style('nova-froala-field', __DIR__ . '/../dist/css/field.css');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../dist/css/froala_styles.min.css' => $this->app->publicPath('css/vendor/froala_styles.min.css'),
            ], 'froala-styles');

            $this->publishes([
                __DIR__ . '/../config/froala.php' => $this->app->configPath('nova/froala.php'),
            ], 'froala-config');

            if (! class_exists('CreateFroalaAttachmentTables')) {
                $timestamp = date('Y_m_d_His', time());

                $this->publishes([
                    __DIR__ . '/../database/migrations/create_froala_attachment_tables.php.stub' => $this->app->databasePath("migrations/{$timestamp}_create_froala_attachment_tables.php"),
                ], 'froala-migrations');
            }
        }
    }

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__ . '/../config/froala.php', 'nova.froala');
    }

    protected function routes(): void
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $this->app['router']->group([
            'middleware' => 'nova',
            'prefix' => 'nova-vendor/froala',
        ], __DIR__ . '/../routes/api.php');
    }
}
