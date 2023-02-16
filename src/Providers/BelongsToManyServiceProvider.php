<?php

namespace Codedor\BelongsToMany\Providers;

use Filament\Facades\Filament;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class BelongsToManyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('filament-belongs-to-many')
            ->hasConfigFile()
            ->setBasePath(__DIR__ . '/../')
            ->hasViews('belongs-to-many-field');
    }

    public function boot()
    {
        parent::boot();

        Filament::serving(function () {
            Filament::registerStyles([__DIR__ . '/../../dist/css/belongs-to-many.css']);
        });
    }
}
