<?php declare(strict_types=1);

namespace Tests;

use Froala\Nova\FroalaServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\ServiceProvider;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\NovaCoreServiceProvider;
use Laravel\Nova\NovaServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class KernelTestCase extends TestCase
{
    use RefreshDatabase;

    final public const DISK = 'public';
    final public const PATH = 'subpath';

    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
            NovaCoreServiceProvider::class,
            NovaServiceProvider::class,
            NovaApplicationServiceProvider::class,
            FroalaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set([
            'database.connections.testbench' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
            'database.default' => 'testbench',

            'froala.disk' => self::DISK,
            'froala.path' => self::PATH,

            'nova.actions' => ['resource' => null],
            'nova.api_middleware' => [],
            'nova.middleware' => [],
        ]);

        Model::unguard();

        Nova::resources([ArticleResource::class]);
    }
}
