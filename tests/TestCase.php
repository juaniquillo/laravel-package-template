<?php

declare(strict_types=1);

namespace Juaniquillo\LaravelPackageTemplate\Tests;

use Illuminate\Foundation\Application;
use Juaniquillo\LaravelPackageTemplate\LaravelPackageTemplateServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LaravelPackageTemplateServiceProvider::class,
        ];
    }
}
