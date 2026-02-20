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
    }

    public function boot(): void
    {
        //
    }
}
