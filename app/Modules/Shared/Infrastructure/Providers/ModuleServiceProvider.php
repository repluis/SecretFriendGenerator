<?php

namespace App\Modules\Shared\Infrastructure\Providers;

use App\Modules\SecretSanta\Domain\Repositories\GameConfigurationRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\PlayerRepositoryInterface;
use App\Modules\SecretSanta\Domain\Repositories\UrlRepositoryInterface;
use App\Modules\SecretSanta\Infrastructure\Persistence\EloquentGameConfigurationRepository;
use App\Modules\SecretSanta\Infrastructure\Persistence\EloquentPlayerRepository;
use App\Modules\SecretSanta\Infrastructure\Persistence\EloquentUrlRepository;
use App\Modules\Fundraising\Domain\Repositories\FundraisingChargeRepositoryInterface;
use App\Modules\Fundraising\Infrastructure\Persistence\EloquentFundraisingChargeRepository;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Infrastructure\Persistence\EloquentTransactionRepository;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Infrastructure\Persistence\EloquentUserRepository;
use App\Modules\Shared\Domain\Repositories\ConfigurationRepositoryInterface;
use App\Modules\Shared\Domain\Services\ConfigurationService;
use App\Modules\Shared\Infrastructure\Persistence\EloquentConfigurationRepository;
use App\Modules\Shared\Presentation\ViewComposers\GlobalConfigComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            PlayerRepositoryInterface::class,
            EloquentPlayerRepository::class
        );

        $this->app->bind(
            UrlRepositoryInterface::class,
            EloquentUrlRepository::class
        );

        $this->app->bind(
            GameConfigurationRepositoryInterface::class,
            EloquentGameConfigurationRepository::class
        );

        // Fundraising module
        $this->app->bind(
            FundraisingChargeRepositoryInterface::class,
            EloquentFundraisingChargeRepository::class
        );

        // Transaction module
        $this->app->bind(
            TransactionRepositoryInterface::class,
            EloquentTransactionRepository::class
        );

        // User module
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        // Auth module
        $this->app->bind(
            \App\Modules\Auth\Domain\Repositories\AuthRepositoryInterface::class,
            \App\Modules\Auth\Infrastructure\Persistence\EloquentAuthRepository::class
        );

        // Configuration module — singleton so DB is hit only once per request
        $this->app->bind(
            ConfigurationRepositoryInterface::class,
            EloquentConfigurationRepository::class
        );

        $this->app->singleton(ConfigurationService::class, function ($app) {
            return new ConfigurationService(
                $app->make(ConfigurationRepositoryInterface::class)
            );
        });
    }

    public function boot(): void
    {
        // Share global config to ALL Blade views (like HandleInertiaRequests for Inertia)
        View::composer('*', GlobalConfigComposer::class);
    }
}
