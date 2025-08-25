<?php

declare(strict_types=1);

use Zotel\Wallet\Internal\Assembler\AvailabilityDtoAssembler;
use Zotel\Wallet\Internal\Assembler\BalanceUpdatedEventAssembler;
use Zotel\Wallet\Internal\Assembler\ExtraDtoAssembler;
use Zotel\Wallet\Internal\Assembler\OptionDtoAssembler;
use Zotel\Wallet\Internal\Assembler\TransactionCreatedEventAssembler;
use Zotel\Wallet\Internal\Assembler\TransactionDtoAssembler;
use Zotel\Wallet\Internal\Assembler\TransactionQueryAssembler;
use Zotel\Wallet\Internal\Assembler\TransferDtoAssembler;
use Zotel\Wallet\Internal\Assembler\TransferLazyDtoAssembler;
use Zotel\Wallet\Internal\Assembler\TransferQueryAssembler;
use Zotel\Wallet\Internal\Events\BalanceUpdatedEvent;
use Zotel\Wallet\Internal\Events\TransactionCreatedEvent;
use Zotel\Wallet\Internal\Events\WalletCreatedEvent;
use Zotel\Wallet\Internal\Repository\TransactionRepository;
use Zotel\Wallet\Internal\Repository\TransferRepository;
use Zotel\Wallet\Internal\Repository\WalletRepository;
use Zotel\Wallet\Internal\Service\ClockService;
use Zotel\Wallet\Internal\Service\ConnectionService;
use Zotel\Wallet\Internal\Service\DatabaseService;
use Zotel\Wallet\Internal\Service\DispatcherService;
use Zotel\Wallet\Internal\Service\IdentifierFactoryService;
use Zotel\Wallet\Internal\Service\JsonService;
use Zotel\Wallet\Internal\Service\LockService;
use Zotel\Wallet\Internal\Service\MathService;
use Zotel\Wallet\Internal\Service\StateService;
use Zotel\Wallet\Internal\Service\StorageService;
use Zotel\Wallet\Internal\Service\TranslatorService;
use Zotel\Wallet\Internal\Service\UuidFactoryService;
use Zotel\Wallet\Internal\Transform\TransactionDtoTransformer;
use Zotel\Wallet\Internal\Transform\TransferDtoTransformer;
use App\Models\WalletTransaction;
use App\Models\WalletTransfer;
use App\Models\Wallet;
use Zotel\Wallet\Services\AssistantService;
use Zotel\Wallet\Services\AtmService;
use Zotel\Wallet\Services\AtomicService;
use Zotel\Wallet\Services\BasketService;
use Zotel\Wallet\Services\BookkeeperService;
use Zotel\Wallet\Services\CastService;
use Zotel\Wallet\Services\ConsistencyService;
use Zotel\Wallet\Services\DiscountService;
use Zotel\Wallet\Services\EagerLoaderService;
use Zotel\Wallet\Services\ExchangeService;
use Zotel\Wallet\Services\FormatterService;
use Zotel\Wallet\Services\PrepareService;
use Zotel\Wallet\Services\PurchaseService;
use Zotel\Wallet\Services\RegulatorService;
use Zotel\Wallet\Services\TaxService;
use Zotel\Wallet\Services\TransactionService;
use Zotel\Wallet\Services\TransferService;
use Zotel\Wallet\Services\WalletService;

return [
    /**
     * Arbitrary Precision Calculator.
     *
     * The 'scale' option defines the number of decimal places
     * that the calculator will use when performing calculations.
     *
     * @see MathService
     */
    'math' => [
        /**
         * The scale of the calculator.
         *
         * @var int
         */
        'scale' => env('WALLET_MATH_SCALE', 64),
    ],

    /**
     * Storage of the state of the balance of wallets.
     *
     * This is used to cache the results of calculations
     * in order to improve the performance of the package.
     *
     * @see StorageService
     */
    'cache' => [
        /**
         * The driver for the cache.
         *
         * @var string
         */
        'driver' => env('WALLET_CACHE_DRIVER', 'array'),

        /**
         * The time to live for the cache in seconds.
         *
         * @var int
         */
        'ttl' => env('WALLET_CACHE_TTL', 24 * 3600),
    ],

    /**
     * A system for dealing with race conditions.
     *
     * This is used to protect against race conditions
     * when updating the balance of a wallet.
     *
     * @see LockService
     */
    'lock' => [
        /**
         * The driver for the lock.
         *
         * The following drivers are supported:
         * - array
         * - redis
         * - memcached
         * - database
         *
         * @var string
         */
        'driver' => env('WALLET_LOCK_DRIVER', 'array'),

        /**
         * The time to live for the lock in seconds.
         *
         * @var int
         */
        'seconds' => env('WALLET_LOCK_TTL', 1),
    ],

    /**
     * Internal services that can be overloaded.
     *
     * This section contains the list of services that can be overloaded by
     * the user. These services are used internally by the package and are
     * critical for it to function properly.
     *
     * @var array<string, class-string>
     */
    'internal' => [
        /**
         * The service for getting the current time.
         *
         * @var string
         */
        'clock' => ClockService::class,

        /**
         * The service for getting the database connection.
         *
         * @var string
         */
        'connection' => ConnectionService::class,

        /**
         * The service for managing the database.
         *
         * @var string
         */
        'database' => DatabaseService::class,

        /**
         * The service for dispatching events.
         *
         * @var string
         */
        'dispatcher' => DispatcherService::class,

        /**
         * The service for serializing and deserializing JSON.
         *
         * @var string
         */
        'json' => JsonService::class,

        /**
         * The service for handling locks.
         *
         * @var string
         */
        'lock' => LockService::class,

        /**
         * The service for performing mathematical operations.
         *
         * @var string
         */
        'math' => MathService::class,

        /**
         * The service for managing the state of the wallet.
         *
         * @var string
         */
        'state' => StateService::class,

        /**
         * The service for managing the storage of the wallet.
         *
         * @var string
         */
        'storage' => StorageService::class,

        /**
         * The service for translating messages.
         *
         * @var string
         */
        'translator' => TranslatorService::class,

        /**
         * The service for generating UUIDs.
         *
         * @var string
         *
         * @deprecated use identifier.
         * @see IdentifierFactoryService
         */
        'uuid' => UuidFactoryService::class,

        /**
         * The service for generating identifiers.
         *
         * @var string
         */
        'identifier' => IdentifierFactoryService::class,
    ],

    /**
     * Services that can be overloaded.
     *
     * Each key is the name of the service, and the value is the fully qualified class name of the service.
     * The default service class is provided here.
     *
     * @var array<string, class-string>
     *
     * @see \Zotel\Wallet\Services
     */
    'services' => [
        // Service for performing operations related to the assistant.
        'assistant' => AssistantService::class,
        // Service for handling ATM operations.
        'atm' => AtmService::class,
        // Service for handling atomic operations.
        'atomic' => AtomicService::class,
        // Service for managing the user's basket.
        'basket' => BasketService::class,
        // Service for handling bookkeeping operations.
        'bookkeeper' => BookkeeperService::class,
        // Service for handling regulation operations.
        'regulator' => RegulatorService::class,
        // Service for casting values.
        'cast' => CastService::class,
        // Service for handling consistency operations.
        'consistency' => ConsistencyService::class,
        // Service for handling discount operations.
        'discount' => DiscountService::class,
        // Service for handling eager loading operations.
        'eager_loader' => EagerLoaderService::class,
        // Service for handling exchange operations.
        'exchange' => ExchangeService::class,
        // Service for formatting values.
        'formatter' => FormatterService::class,
        // Service for preparing operations.
        'prepare' => PrepareService::class,
        // Service for handling purchase operations.
        'purchase' => PurchaseService::class,
        // Service for handling tax operations.
        'tax' => TaxService::class,
        // Service for handling transaction operations.
        'transaction' => TransactionService::class,
        // Service for handling transfer operations.
        'transfer' => TransferService::class,
        // Service for managing wallet operations.
        'wallet' => WalletService::class,
    ],

    /**
     * Repositories for fetching data from the database.
     *
     * Each repository is responsible for fetching data from the database for a specific entity.
     *
     * @see \Zotel\Wallet\Interfaces\Wallet
     * @see \Zotel\Wallet\Interfaces\Transaction
     * @see \Zotel\Wallet\Interfaces\WalletTransfer
     */
    'repositories' => [
        /**
         * Repository for fetching transaction data.
         *
         * @see \Zotel\Wallet\Interfaces\Transaction
         */
        'transaction' => TransactionRepository::class,
        /**
         * Repository for fetching transfer data.
         *
         * @see \Zotel\Wallet\Interfaces\WalletTransfer
         */
        'transfer' => TransferRepository::class,
        /**
         * Repository for fetching wallet data.
         *
         * @see \Zotel\Wallet\Interfaces\Wallet
         */
        'wallet' => WalletRepository::class,
    ],

    /**
     * Defines the mapping of DTO (Data WalletTransfer Object) types to their respective transformer classes.
     * Transformers are used to convert DTOs into a structured array format, suitable for further processing
     * or output. This allows for a clean separation between the internal data representation and the format
     * required by clients or external systems.
     */
    'transformers' => [
        /**
         * Transformer for converting transaction DTOs.
         * This transformer handles the conversion of transaction data, ensuring that all necessary
         * information is presented in a structured and consistent manner for downstream processing.
         */
        'transaction' => TransactionDtoTransformer::class,

        /**
         * Transformer for converting transfer DTOs.
         * Similar to the transaction transformer, this class is responsible for taking transfer-related
         * DTOs and converting them into a standardized array format. This is essential for operations
         * involving the movement of funds or assets between accounts or entities.
         */
        'transfer' => TransferDtoTransformer::class,
    ],

    /**
     * Builder class, needed to create DTO.
     */
    'assemblers' => [
        /**
         * Assembler for creating Availability DTO.
         */
        'availability' => AvailabilityDtoAssembler::class,
        /**
         * Assembler for creating Balance Updated Event DTO.
         */
        'balance_updated_event' => BalanceUpdatedEventAssembler::class,
        /**
         * Assembler for creating Extra DTO.
         */
        'extra' => ExtraDtoAssembler::class,
        /**
         * Assembler for creating Option DTO.
         */
        'option' => OptionDtoAssembler::class,
        /**
         * Assembler for creating Transaction DTO.
         */
        'transaction' => TransactionDtoAssembler::class,
        /**
         * Assembler for creating WalletTransfer Lazy DTO.
         */
        'transfer_lazy' => TransferLazyDtoAssembler::class,
        /**
         * Assembler for creating WalletTransfer DTO.
         */
        'transfer' => TransferDtoAssembler::class,
        /**
         * Assembler for creating Transaction Created Event DTO.
         */
        'transaction_created_event' => TransactionCreatedEventAssembler::class,
        /**
         * Assembler for creating Transaction Query DTO.
         */
        'transaction_query' => TransactionQueryAssembler::class,
        /**
         * Assembler for creating WalletTransfer Query DTO.
         */
        'transfer_query' => TransferQueryAssembler::class,
    ],

    /**
     * Package system events.
     *
     * @var array<string, class-string>
     */
    'events' => [
        /**
         * The event triggered when the balance is updated.
         */
        'balance_updated' => BalanceUpdatedEvent::class,

        /**
         * The event triggered when a wallet is created.
         */
        'wallet_created' => WalletCreatedEvent::class,

        /**
         * The event triggered when a transaction is created.
         */
        'transaction_created' => TransactionCreatedEvent::class,
    ],

    /**
     * Base model 'transaction'.
     *
     * @see Transaction
     */
    'transaction' => [
        /**
         * The table name for transactions.
         *
         * This value is used to store transactions in a database.
         *
         * @see Transaction
         */
        'table' => env('WALLET_TRANSACTION_TABLE_NAME', 'transactions'),

        /**
         * The model class for transactions.
         *
         * This value is used to create new transactions.
         *
         * @see Transaction
         */
        'model' => Transaction::class,
    ],

    /**
     * Base model 'transfer'.
     *
     * Contains the configuration for the transfer model.
     *
     * @see WalletTransfer
     */
    'transfer' => [
        /**
         * The table name for transfers.
         *
         * This value is used to store transfers in a database.
         *
         * @see WalletTransfer
         */
        'table' => env('WALLET_TRANSFER_TABLE_NAME', 'transfers'),

        /**
         * The model class for transfers.
         *
         * This value is used to create new transfers.
         *
         * @see WalletTransfer
         */
        'model' => WalletTransfer::class,
    ],

    /**
     * Base model 'wallet'.
     *
     * Contains the configuration for the wallet model.
     *
     * @see Wallet
     */
    'wallet' => [
        /**
         * The table name for wallets.
         *
         * This value is used to store wallets in a database.
         *
         * @see Wallet
         */
        'table' => env('WALLET_WALLET_TABLE_NAME', 'wallets'),

        /**
         * The model class for wallets.
         *
         * This value is used to create new wallets.
         *
         * @see Wallet
         */
        'model' => Wallet::class,

        /**
         * The configuration options for creating wallets.
         *
         * @var array<string, mixed>
         */
        'creating' => [],

        /**
         * The default configuration for wallets.
         *
         * @var array<string, mixed>
         */
        'default' => [
            /**
             * The name of the default wallet.
             *
             * @var string
             */
            'name' => env('WALLET_DEFAULT_WALLET_NAME', 'Default Wallet'),

            /**
             * The slug of the default wallet.
             *
             * @var string
             */
            'slug' => env('WALLET_DEFAULT_WALLET_SLUG', 'default'),

            /**
             * The meta information of the default wallet.
             *
             * @var array<string, mixed>
             */
            'meta' => [],
        ],
    ],
];
