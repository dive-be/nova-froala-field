<?php declare(strict_types=1);

namespace Tests\Fixtures;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;

final class TestServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app[Repository::class]->set('nova.froala-field.attachments_driver', 'trix');
    }
}
