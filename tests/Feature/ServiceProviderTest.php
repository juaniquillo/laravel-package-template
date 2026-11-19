<?php

declare(strict_types=1);

use Juaniquillo\LaravelPackageTemplate\LaravelPackageTemplateServiceProvider;

it('registers the service provider', function () {
    $provider = $this->app->getProvider(LaravelPackageTemplateServiceProvider::class);

    expect($provider)->toBeInstanceOf(LaravelPackageTemplateServiceProvider::class);
});

it('boots the service provider', function () {
    $loadedProviders = $this->app->getLoadedProviders();

    expect($loadedProviders[LaravelPackageTemplateServiceProvider::class] ?? false)->toBeTrue();
});
