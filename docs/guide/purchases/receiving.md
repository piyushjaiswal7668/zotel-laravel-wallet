# Payment. Customize receiving

The architecture of laravel wallet is designed in such a way that when purchasing goods, wallets are created and all money is credited to them, but it happens that this is not necessary. In this case, this functionality will help. You specify the wallet for depositing funds and the buyer will transfer money to this wallet when purchasing a product. This is convenient for marketplaces.

## User Model

Add the `CanPay` trait and `Customer` interface to your User model.

> The trait `CanPay` already inherits `HasWallet`, reuse will cause an error.

```php
use Zotel\Wallet\Traits\CanPay;
use Zotel\Wallet\Interfaces\Customer;

class User extends Model implements Customer
{
    use CanPay;
}
```

## Item Model

Add the `HasWallet` trait and interface to `Item` model.
If we want to achieve multi wallets for a product, then we need to add `HasWallets`.

Starting from version 9.x there are two product interfaces:
- For an unlimited number of products (`ProductInterface`);
- For a limited number of products (`ProductLimitedInterface`);

An example with an unlimited number of products:
```php
use Zotel\Wallet\Traits\HasWallet;
use Zotel\Wallet\Traits\HasWallets;
use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Interfaces\ProductInterface;

class Item extends Model implements ProductInterface
{
    use HasWallet, HasWallets;

    public function getAmountProduct(Customer $customer): int|string
    {
        return 100;
    }

    public function getMetaProduct(): ?array
    {
        return [
            'title' => $this->title, 
            'description' => 'Purchase of Product #' . $this->id,
        ];
    }
}
```

Example with a limited number of products:
```php
use Zotel\Wallet\Traits\HasWallet;
use Zotel\Wallet\Traits\HasWallets;
use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Interfaces\ProductLimitedInterface;

class Item extends Model implements ProductLimitedInterface
{
    use HasWallet, HasWallets;

    public function canBuy(Customer $customer, int $quantity = 1, bool $force = false): bool
    {
        return true; 
    }
    
    public function getAmountProduct(Customer $customer): int|string
    {
        return 100;
    }

    public function getMetaProduct(): ?array
    {
        return [
            'title' => $this->title, 
            'description' => 'Purchase of Product #' . $this->id,
        ];
    }
}
```

I do not recommend using the limited interface when working with a shopping cart.
If you are working with a shopping cart, then you should override the `PurchaseServiceInterface` interface.
With it, you can check the availability of all products with one request, there will be no N-queries in the database.

## Proceed to purchase

Find the user and check the balance.

```php
$user = User::first();
$user->balance; // 100
```

Find the goods and check the cost.

```php
$item = Item::first();
$item->getAmountProduct($user); // 100

$receiving = $item->createWallet([
    'name' => 'Dollar',
    'meta' => [
        'currency' => 'USD',
    ],
]);
```

The user can buy a product, buy...

```php
$cart = app(Cart::class)
    ->withItem($item, receiving: $receiving)
;

$user->payCart($cart);
$user->balance; // 0

$receiving->balanceInt; // $100
```

It's simple!
