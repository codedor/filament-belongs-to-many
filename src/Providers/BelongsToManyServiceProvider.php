<?php

namespace Codedor\BelongsToMany\Providers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BelongsToManyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-belongs-to-many')
            ->setBasePath(__DIR__ . '/../')
            ->hasViews('belongs-to-many-field');
    }
}
