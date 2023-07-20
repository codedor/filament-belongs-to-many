<?php

namespace Codedor\BelongsToMany\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
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

    public function bootingPackage()
    {
        FilamentAsset::register([
            Css::make('filament-belongs-to-many-stylesheet', __DIR__ . '/../../dist/css/belongs-to-many.css'),
        ], 'filament-belongs-to-many');
    }
}
