It is necessary to expand the model that will have the wallet.
This is done in two stages:
  - Add `Wallet` interface;
  - Add the `HasWalletFloat` trait;

Let's get started.
```php
use Zotel\Wallet\Traits\HasWalletFloat;
use Zotel\Wallet\Interfaces\Wallet;

class User extends Model implements Wallet
{
    use HasWalletFloat;
}
```

The model is prepared to work with a wallet.
