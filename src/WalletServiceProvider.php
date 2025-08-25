<?php

declare(strict_types=1);

namespace Zotel\Wallet;

use Zotel\Wallet\External\Api\TransactionQueryHandler;
use Zotel\Wallet\External\Api\TransactionQueryHandlerInterface;
use Zotel\Wallet\External\Api\TransferQueryHandler;
use Zotel\Wallet\External\Api\TransferQueryHandlerInterface;
use Zotel\Wallet\Internal\Assembler\AvailabilityDtoAssembler;
use Zotel\Wallet\Internal\Assembler\AvailabilityDtoAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\BalanceUpdatedEventAssembler;
use Zotel\Wallet\Internal\Assembler\BalanceUpdatedEventAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\ExtraDtoAssembler;
use Zotel\Wallet\Internal\Assembler\ExtraDtoAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\OptionDtoAssembler;
use Zotel\Wallet\Internal\Assembler\OptionDtoAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\TransactionCreatedEventAssembler;
use Zotel\Wallet\Internal\Assembler\TransactionCreatedEventAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\TransactionDtoAssembler;
use Zotel\Wallet\Internal\Assembler\TransactionDtoAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\TransactionQueryAssembler;
use Zotel\Wallet\Internal\Assembler\TransactionQueryAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\TransferDtoAssembler;
use Zotel\Wallet\Internal\Assembler\TransferDtoAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\TransferLazyDtoAssembler;
use Zotel\Wallet\Internal\Assembler\TransferLazyDtoAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\TransferQueryAssembler;
use Zotel\Wallet\Internal\Assembler\TransferQueryAssemblerInterface;
use Zotel\Wallet\Internal\Assembler\WalletCreatedEventAssembler;
use Zotel\Wallet\Internal\Assembler\WalletCreatedEventAssemblerInterface;
use Zotel\Wallet\Internal\Decorator\StorageServiceLockDecorator;
use Zotel\Wallet\Internal\Events\BalanceUpdatedEvent;
use Zotel\Wallet\Internal\Events\BalanceUpdatedEventInterface;
use Zotel\Wallet\Internal\Events\TransactionCreatedEvent;
use Zotel\Wallet\Internal\Events\TransactionCreatedEventInterface;
use Zotel\Wallet\Internal\Events\WalletCreatedEvent;
use Zotel\Wallet\Internal\Events\WalletCreatedEventInterface;
use Zotel\Wallet\Internal\Repository\TransactionRepository;
use Zotel\Wallet\Internal\Repository\TransactionRepositoryInterface;
use Zotel\Wallet\Internal\Repository\TransferRepository;
use Zotel\Wallet\Internal\Repository\TransferRepositoryInterface;
use Zotel\Wallet\Internal\Repository\WalletRepository;
use Zotel\Wallet\Internal\Repository\WalletRepositoryInterface;
use Zotel\Wallet\Internal\Service\ClockService;
use Zotel\Wallet\Internal\Service\ClockServiceInterface;
use Zotel\Wallet\Internal\Service\ConnectionService;
use Zotel\Wallet\Internal\Service\ConnectionServiceInterface;
use Zotel\Wallet\Internal\Service\DatabaseService;
use Zotel\Wallet\Internal\Service\DatabaseServiceInterface;
use Zotel\Wallet\Internal\Service\DispatcherService;
use Zotel\Wallet\Internal\Service\DispatcherServiceInterface;
use Zotel\Wallet\Internal\Service\IdentifierFactoryService;
use Zotel\Wallet\Internal\Service\IdentifierFactoryServiceInterface;
use Zotel\Wallet\Internal\Service\JsonService;
use Zotel\Wallet\Internal\Service\JsonServiceInterface;
use Zotel\Wallet\Internal\Service\LockService;
use Zotel\Wallet\Internal\Service\LockServiceInterface;
use Zotel\Wallet\Internal\Service\MathService;
use Zotel\Wallet\Internal\Service\MathServiceInterface;
use Zotel\Wallet\Internal\Service\StateService;
use Zotel\Wallet\Internal\Service\StateServiceInterface;
use Zotel\Wallet\Internal\Service\StorageService;
use Zotel\Wallet\Internal\Service\StorageServiceInterface;
use Zotel\Wallet\Internal\Service\TranslatorService;
use Zotel\Wallet\Internal\Service\TranslatorServiceInterface;
use Zotel\Wallet\Internal\Service\UuidFactoryService;
use Zotel\Wallet\Internal\Service\UuidFactoryServiceInterface;
use Zotel\Wallet\Internal\Transform\TransactionDtoTransformer;
use Zotel\Wallet\Internal\Transform\TransactionDtoTransformerInterface;
use Zotel\Wallet\Internal\Transform\TransferDtoTransformer;
use Zotel\Wallet\Internal\Transform\TransferDtoTransformerInterface;
use App\Models\WalletTransaction;
use App\Models\WalletTransfer;
use App\Models\Wallet;
use Zotel\Wallet\Services\AssistantService;
use Zotel\Wallet\Services\AssistantServiceInterface;
use Zotel\Wallet\Services\AtmService;
use Zotel\Wallet\Services\AtmServiceInterface;
use Zotel\Wallet\Services\AtomicService;
use Zotel\Wallet\Services\AtomicServiceInterface;
use Zotel\Wallet\Services\BasketService;
use Zotel\Wallet\Services\BasketServiceInterface;
use Zotel\Wallet\Services\BookkeeperService;
use Zotel\Wallet\Services\BookkeeperServiceInterface;
use Zotel\Wallet\Services\CastService;
use Zotel\Wallet\Services\CastServiceInterface;
use Zotel\Wallet\Services\ConsistencyService;
use Zotel\Wallet\Services\ConsistencyServiceInterface;
use Zotel\Wallet\Services\DiscountService;
use Zotel\Wallet\Services\DiscountServiceInterface;
use Zotel\Wallet\Services\EagerLoaderService;
use Zotel\Wallet\Services\EagerLoaderServiceInterface;
use Zotel\Wallet\Services\ExchangeService;
use Zotel\Wallet\Services\ExchangeServiceInterface;
use Zotel\Wallet\Services\FormatterService;
use Zotel\Wallet\Services\FormatterServiceInterface;
use Zotel\Wallet\Services\PrepareService;
use Zotel\Wallet\Services\PrepareServiceInterface;
use Zotel\Wallet\Services\PurchaseService;
use Zotel\Wallet\Services\PurchaseServiceInterface;
use Zotel\Wallet\Services\RegulatorService;
use Zotel\Wallet\Services\RegulatorServiceInterface;
use Zotel\Wallet\Services\TaxService;
use Zotel\Wallet\Services\TaxServiceInterface;
use Zotel\Wallet\Services\TransactionService;
use Zotel\Wallet\Services\TransactionServiceInterface;
use Zotel\Wallet\Services\TransferService;
use Zotel\Wallet\Services\TransferServiceInterface;
use Zotel\Wallet\Services\WalletService;
use Zotel\Wallet\Services\WalletServiceInterface;
use function config;
use function dirname;
use function function_exists;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Events\TransactionBeginning;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Database\Events\TransactionCommitting;
use Illuminate\Database\Events\TransactionRolledBack;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class WalletServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(dirname(__DIR__).'/resources/lang', 'wallet');

        Event::listen(TransactionBeginning::class, Internal\Listeners\TransactionBeginningListener::class);
        Event::listen(TransactionCommitting::class, Internal\Listeners\TransactionCommittingListener::class);
        Event::listen(TransactionCommitted::class, Internal\Listeners\TransactionCommittedListener::class);
        Event::listen(TransactionRolledBack::class, Internal\Listeners\TransactionRolledBackListener::class);

        // @codeCoverageIgnoreStart
        if (! $this->app->runningInConsole()) {
            return;
        }
        // @codeCoverageIgnoreEnd

        if (WalletConfigure::isRunsMigrations()) {
            $this->loadMigrationsFrom([dirname(__DIR__).'/database']);
        }

        if (function_exists('config_path')) {
            $this->publishes([
                dirname(__DIR__).'/config/config.php' => config_path('wallet.php'),
            ], 'laravel-wallet-config');
        }

        $this->publishes([
            dirname(__DIR__).'/database/' => database_path('migrations'),
        ], 'laravel-wallet-migrations');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/config.php', 'wallet');

        /**
         * @var array{
         *     internal?: array<class-string|null>,
         *     services?: array<class-string|null>,
         *     cache?: array{driver: string|null},
         *     repositories?: array<class-string|null>,
         *     transformers?: array<class-string|null>,
         *     assemblers?: array<class-string|null>,
         *     events?: array<class-string|null>,
         *     transaction?: array{model?: class-string|null},
         *     transfer?: array{model?: class-string|null},
         *     wallet?: array{model?: class-string|null},
         * } $configure
         */
        $configure = config('wallet', []);

        $this->internal($configure['internal'] ?? []);
        $this->services($configure['services'] ?? [], $configure['cache'] ?? []);

        $this->repositories($configure['repositories'] ?? []);
        $this->transformers($configure['transformers'] ?? []);
        $this->assemblers($configure['assemblers'] ?? []);
        $this->events($configure['events'] ?? []);

        $this->bindObjects($configure);
    }

    /**
     * @return class-string[]
     */
    public function provides(): array
    {
        return array_merge(
            $this->internalProviders(),
            $this->servicesProviders(),
            $this->repositoriesProviders(),
            $this->transformersProviders(),
            $this->assemblersProviders(),
            $this->eventsProviders(),
            $this->bindObjectsProviders(),
        );
    }

    /**
     * @param array<class-string|null> $configure
     */
    private function repositories(array $configure): void
    {
        $this->app->singleton(
            TransactionRepositoryInterface::class,
            $configure['transaction'] ?? TransactionRepository::class
        );

        $this->app->singleton(
            TransferRepositoryInterface::class,
            $configure['transfer'] ?? TransferRepository::class
        );

        $this->app->singleton(WalletRepositoryInterface::class, $configure['wallet'] ?? WalletRepository::class);
    }

    /**
     * @param array<class-string|null> $configure
     */
    private function internal(array $configure): void
    {
        $this->app->alias($configure['storage'] ?? StorageService::class, 'wallet.internal.storage');
        $this->app->when($configure['storage'] ?? StorageService::class)
            ->needs('$ttl')
            ->giveConfig('wallet.cache.ttl');

        $this->app->singleton(ClockServiceInterface::class, $configure['clock'] ?? ClockService::class);
        $this->app->singleton(ConnectionServiceInterface::class, $configure['connection'] ?? ConnectionService::class);
        $this->app->singleton(DatabaseServiceInterface::class, $configure['database'] ?? DatabaseService::class);
        $this->app->singleton(DispatcherServiceInterface::class, $configure['dispatcher'] ?? DispatcherService::class);
        $this->app->singleton(JsonServiceInterface::class, $configure['json'] ?? JsonService::class);

        $this->app->when($configure['lock'] ?? LockService::class)
            ->needs('$seconds')
            ->giveConfig('wallet.lock.seconds', 1);

        $this->app->singleton(LockServiceInterface::class, $configure['lock'] ?? LockService::class);

        $this->app->when($configure['math'] ?? MathService::class)
            ->needs('$scale')
            ->giveConfig('wallet.math.scale', 64);

        $this->app->singleton(MathServiceInterface::class, $configure['math'] ?? MathService::class);
        $this->app->singleton(StateServiceInterface::class, $configure['state'] ?? StateService::class);
        $this->app->singleton(TranslatorServiceInterface::class, $configure['translator'] ?? TranslatorService::class);
        $this->app->singleton(UuidFactoryServiceInterface::class, $configure['uuid'] ?? UuidFactoryService::class);
        $this->app->singleton(
            IdentifierFactoryServiceInterface::class,
            $configure['identifier'] ?? IdentifierFactoryService::class
        );
    }

    /**
     * @param array<class-string|null> $configure
     * @param array{driver?: string|null} $cache
     */
    private function services(array $configure, array $cache): void
    {
        $this->app->singleton(AssistantServiceInterface::class, $configure['assistant'] ?? AssistantService::class);
        $this->app->singleton(AtmServiceInterface::class, $configure['atm'] ?? AtmService::class);
        $this->app->singleton(AtomicServiceInterface::class, $configure['atomic'] ?? AtomicService::class);
        $this->app->singleton(BasketServiceInterface::class, $configure['basket'] ?? BasketService::class);
        $this->app->singleton(CastServiceInterface::class, $configure['cast'] ?? CastService::class);
        $this->app->singleton(
            ConsistencyServiceInterface::class,
            $configure['consistency'] ?? ConsistencyService::class
        );
        $this->app->singleton(DiscountServiceInterface::class, $configure['discount'] ?? DiscountService::class);
        $this->app->singleton(
            EagerLoaderServiceInterface::class,
            $configure['eager_loader'] ?? EagerLoaderService::class
        );
        $this->app->singleton(ExchangeServiceInterface::class, $configure['exchange'] ?? ExchangeService::class);
        $this->app->singleton(FormatterServiceInterface::class, $configure['formatter'] ?? FormatterService::class);
        $this->app->singleton(PrepareServiceInterface::class, $configure['prepare'] ?? PrepareService::class);
        $this->app->singleton(PurchaseServiceInterface::class, $configure['purchase'] ?? PurchaseService::class);
        $this->app->singleton(TaxServiceInterface::class, $configure['tax'] ?? TaxService::class);
        $this->app->singleton(
            TransactionServiceInterface::class,
            $configure['transaction'] ?? TransactionService::class
        );
        $this->app->singleton(TransferServiceInterface::class, $configure['transfer'] ?? TransferService::class);
        $this->app->singleton(WalletServiceInterface::class, $configure['wallet'] ?? WalletService::class);

        // bookkeepper service
        $this->app->when(StorageServiceLockDecorator::class)
            ->needs(StorageServiceInterface::class)
            ->give(function () use ($cache) {
                return $this->app->make(
                    'wallet.internal.storage',
                    [
                        'cacheRepository' => $this->app->get(CacheFactory::class)
                            ->store($cache['driver'] ?? 'array'),
                    ],
                );
            });

        $this->app->when($configure['bookkeeper'] ?? BookkeeperService::class)
            ->needs(StorageServiceInterface::class)
            ->give(StorageServiceLockDecorator::class);

        $this->app->singleton(BookkeeperServiceInterface::class, $configure['bookkeeper'] ?? BookkeeperService::class);

        // regulator service
        $this->app->when($configure['regulator'] ?? RegulatorService::class)
            ->needs(StorageServiceInterface::class)
            ->give(function () {
                return $this->app->make(
                    'wallet.internal.storage',
                    [
                        'cacheRepository' => clone $this->app->make(CacheFactory::class)
                            ->store('array'),
                    ],
                );
            });

        $this->app->singleton(RegulatorServiceInterface::class, $configure['regulator'] ?? RegulatorService::class);
    }

    /**
     * @param array<class-string|null> $configure
     */
    private function assemblers(array $configure): void
    {
        $this->app->singleton(
            AvailabilityDtoAssemblerInterface::class,
            $configure['availability'] ?? AvailabilityDtoAssembler::class
        );

        $this->app->singleton(
            BalanceUpdatedEventAssemblerInterface::class,
            $configure['balance_updated_event'] ?? BalanceUpdatedEventAssembler::class
        );

        $this->app->singleton(ExtraDtoAssemblerInterface::class, $configure['extra'] ?? ExtraDtoAssembler::class);

        $this->app->singleton(
            OptionDtoAssemblerInterface::class,
            $configure['option'] ?? OptionDtoAssembler::class
        );

        $this->app->singleton(
            TransactionDtoAssemblerInterface::class,
            $configure['transaction'] ?? TransactionDtoAssembler::class
        );

        $this->app->singleton(
            TransferLazyDtoAssemblerInterface::class,
            $configure['transfer_lazy'] ?? TransferLazyDtoAssembler::class
        );

        $this->app->singleton(
            TransferDtoAssemblerInterface::class,
            $configure['transfer'] ?? TransferDtoAssembler::class
        );

        $this->app->singleton(
            TransactionQueryAssemblerInterface::class,
            $configure['transaction_query'] ?? TransactionQueryAssembler::class
        );

        $this->app->singleton(
            TransferQueryAssemblerInterface::class,
            $configure['transfer_query'] ?? TransferQueryAssembler::class
        );

        $this->app->singleton(
            WalletCreatedEventAssemblerInterface::class,
            $configure['wallet_created_event'] ?? WalletCreatedEventAssembler::class
        );

        $this->app->singleton(
            TransactionCreatedEventAssemblerInterface::class,
            $configure['transaction_created_event'] ?? TransactionCreatedEventAssembler::class
        );
    }

    /**
     * @param array<class-string|null> $configure
     */
    private function transformers(array $configure): void
    {
        $this->app->singleton(
            TransactionDtoTransformerInterface::class,
            $configure['transaction'] ?? TransactionDtoTransformer::class
        );

        $this->app->singleton(
            TransferDtoTransformerInterface::class,
            $configure['transfer'] ?? TransferDtoTransformer::class
        );
    }

    /**
     * @param array<class-string|null> $configure
     */
    private function events(array $configure): void
    {
        $this->app->bind(
            BalanceUpdatedEventInterface::class,
            $configure['balance_updated'] ?? BalanceUpdatedEvent::class
        );

        $this->app->bind(
            WalletCreatedEventInterface::class,
            $configure['wallet_created'] ?? WalletCreatedEvent::class
        );

        $this->app->bind(
            TransactionCreatedEventInterface::class,
            $configure['transaction_created'] ?? TransactionCreatedEvent::class
        );
    }

    /**
     * @param array{
     *     transaction?: array{model?: class-string|null},
     *     transfer?: array{model?: class-string|null},
     *     wallet?: array{model?: class-string|null},
     * } $configure
     */
    private function bindObjects(array $configure): void
    {
        $this->app->bind(WalletTransaction::class, $configure['transaction']['model'] ?? null);
        $this->app->bind(WalletTransfer::class, $configure['transfer']['model'] ?? null);
        $this->app->bind(Wallet::class, $configure['wallet']['model'] ?? null);

        // api
        $this->app->bind(TransactionQueryHandlerInterface::class, TransactionQueryHandler::class);
        $this->app->bind(TransferQueryHandlerInterface::class, TransferQueryHandler::class);
    }

    /**
     * @return class-string[]
     */
    private function internalProviders(): array
    {
        return [
            ClockServiceInterface::class,
            ConnectionServiceInterface::class,
            DatabaseServiceInterface::class,
            DispatcherServiceInterface::class,
            JsonServiceInterface::class,
            LockServiceInterface::class,
            MathServiceInterface::class,
            StateServiceInterface::class,
            TranslatorServiceInterface::class,
            UuidFactoryServiceInterface::class,
            IdentifierFactoryServiceInterface::class,
        ];
    }

    /**
     * @return class-string[]
     */
    private function servicesProviders(): array
    {
        return [
            AssistantServiceInterface::class,
            AtmServiceInterface::class,
            AtomicServiceInterface::class,
            BasketServiceInterface::class,
            CastServiceInterface::class,
            ConsistencyServiceInterface::class,
            DiscountServiceInterface::class,
            EagerLoaderServiceInterface::class,
            ExchangeServiceInterface::class,
            FormatterServiceInterface::class,
            PrepareServiceInterface::class,
            PurchaseServiceInterface::class,
            TaxServiceInterface::class,
            TransactionServiceInterface::class,
            TransferServiceInterface::class,
            WalletServiceInterface::class,

            BookkeeperServiceInterface::class,
            RegulatorServiceInterface::class,
        ];
    }

    /**
     * @return class-string[]
     */
    private function repositoriesProviders(): array
    {
        return [
            TransactionRepositoryInterface::class,
            TransferRepositoryInterface::class,
            WalletRepositoryInterface::class,
        ];
    }

    /**
     * @return class-string[]
     */
    private function transformersProviders(): array
    {
        return [
            AvailabilityDtoAssemblerInterface::class,
            BalanceUpdatedEventAssemblerInterface::class,
            ExtraDtoAssemblerInterface::class,
            OptionDtoAssemblerInterface::class,
            TransactionDtoAssemblerInterface::class,
            TransferLazyDtoAssemblerInterface::class,
            TransferDtoAssemblerInterface::class,
            TransactionQueryAssemblerInterface::class,
            TransferQueryAssemblerInterface::class,
            WalletCreatedEventAssemblerInterface::class,
            TransactionCreatedEventAssemblerInterface::class,
        ];
    }

    /**
     * @return class-string[]
     */
    private function assemblersProviders(): array
    {
        return [TransactionDtoTransformerInterface::class, TransferDtoTransformerInterface::class];
    }

    /**
     * @return class-string[]
     */
    private function eventsProviders(): array
    {
        return [
            BalanceUpdatedEventInterface::class,
            WalletCreatedEventInterface::class,
            TransactionCreatedEventInterface::class,
        ];
    }

    /**
     * @return class-string[]
     */
    private function bindObjectsProviders(): array
    {
        return [TransactionQueryHandlerInterface::class, TransferQueryHandlerInterface::class];
    }
}
