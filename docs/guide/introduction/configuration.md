# Configuration

Though this package is crafted to suit most of your needs by default, you can edit the configuration file to suit certain demands.

## Environment

| Name                            | Description                   | Default        | 
|---------------------------------|-------------------------------|----------------|
| `WALLET_MATH_SCALE`             | Select mathematical precision | 64             |
| `WALLET_CACHE_DRIVER`           | Cache for wallet balance      | array          |
| `WALLET_CACHE_TTL`              | Cache TTL for wallet balance  | 24h            |
| `WALLET_LOCK_DRIVER`            | Lock for wallets              | array          |
| `WALLET_LOCK_TTL`               | Lock TTL for wallets          | 1s             |
| `WALLET_TRANSACTION_TABLE_NAME` | Transaction table name        | transactions   |
| `WALLET_TRANSFER_TABLE_NAME`    | Transfer table name           | transfers      |
| `WALLET_WALLET_TABLE_NAME`      | Wallet table name             | wallets        |
| `WALLET_DEFAULT_WALLET_NAME`    | Default wallet name           | Default Wallet |
| `WALLET_DEFAULT_WALLET_SLUG`    | Default wallet slug           | default        |

## Configure default wallet
Customize `name`,`slug` and `meta` of default wallet.

config/wallet.php:
```php
'default' => [
    'name' => 'Ethereum',
    'slug' => 'ETH',
    'meta' => [],
],
```
## Extend base Wallet model
You can extend base Wallet model by creating a new class that extends `App\Models\Wallet` and registering the new class in `config/wallet.php`.
Example `MyWallet.php`

App/Models/MyWallet.php:
```php
use App\Models\Wallet as WalletBase;

class MyWallet extends WalletBase {
    public function helloWorld(): string { return "hello world"; }
}
```
### Register base Wallet model

config/wallet.php:
```php
'wallet' => [
    'table' => 'wallets',
    'model' => MyWallet::class,
    'creating' => [],
    'default' => [
        'name' => 'Default Wallet',
        'slug' => 'default',
        'meta' => [],
    ],
],
```
```php
echo $user->wallet->helloWorld();
```
This same method above, can be used to extend the base `Transfer` and `Transaction` models and registering the extended models in the configuration file.
### Changing wallet decimal places

You can change the default wallet decimal places, in wallet config file. This can be useful when working with fractional numbers.

config/wallet.php:
```php
/**
 * Base model 'wallet'.
 */
'wallet' => [
    ....
    'creating' => [
        'decimal_places' => 18,
    ],
   ....
],
```

