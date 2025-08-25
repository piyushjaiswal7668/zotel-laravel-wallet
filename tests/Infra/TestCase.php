<?php

declare(strict_types=1);

namespace Bavix\Wallet\Test\Infra;

use Bavix\Wallet\Test\Infra\PackageModels\WalletTransaction;
use Bavix\Wallet\Test\Infra\PackageModels\WalletTransfer;
use Bavix\Wallet\Test\Infra\PackageModels\Wallet;
use Bavix\Wallet\Test\Infra\Services\MyExchangeService;
use Bavix\Wallet\WalletServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * @internal
 */
abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::transactionLevel() && DB::rollBack();
    }

    final public function expectExceptionMessageStrict(mixed $message): void
    {
        assert(is_string($message));

        $this->expectExceptionMessageMatches("~^{$message}$~");
    }

    /**
     * @param Application $app
     * @return non-empty-array<int, string>
     */
    final protected function getPackageProviders($app): array
    {
        // Bind eloquent models to IoC container
        $app['config']->set('wallet.services.exchange', MyExchangeService::class);
        $app['config']->set('wallet.transaction.model', WalletTransaction::class);
        $app['config']->set('wallet.transfer.model', WalletTransfer::class);
        $app['config']->set('wallet.wallet.model', Wallet::class);

        return [WalletServiceProvider::class, TestServiceProvider::class];
    }

    /**
     * @param Application $app
     */
    final protected function getEnvironmentSetUp($app): void
    {
        /** @var Repository $config */
        $config = $app['config'];

        // database
        $config->set('database.connections.testing.prefix', 'tests');
        $config->set('database.connections.pgsql.prefix', 'tests');
        $config->set('database.connections.mysql.prefix', 'tests');
        $config->set('database.connections.mariadb.prefix', 'tests');
        $config->set('database.connections.mariadb.port', 3307);

        // new table name's
        $config->set('wallet.transaction.table', 'transaction');
        $config->set('wallet.transfer.table', 'transfer');
        $config->set('wallet.wallet.table', 'wallet');
    }
}
