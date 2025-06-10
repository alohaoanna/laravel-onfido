<?php

namespace OANNA\Onfido;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use OANNA\Onfido\Commands\OnfidoCommand;
use OANNA\Onfido\Facades\Onfido;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OnfidoServiceProvider extends PackageServiceProvider
{
    public function registeringPackage(): void
    {
        parent::registeringPackage();

        $this->app->alias(OnfidoManager::class, 'onfido');
        $this->app->singleton(OnfidoManager::class);
        AliasLoader::getInstance()->alias('Onfido', Onfido::class);
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-onfido')
            ->hasConfigFile()
            ->hasMigration('create_onfido_instances_table')
            ->runsMigrations()
            ->hasCommand(OnfidoCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info("laravel-onfido package is installing...");
                    })
                    ->publishConfigFile()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->endWith(function (InstallCommand $command) {
                        $command->info("Thanks for using laravel-onfido package!");
                    });
            });
    }
}
