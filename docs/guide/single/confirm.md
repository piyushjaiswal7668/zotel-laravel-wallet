# Confirm Transaction

There are situations when it is necessary to create a transaction without crediting to the wallet or debiting. Laravel-wallet has such a mode of unconfirmed transactions.

You create a transaction without confirmation, and a little later you confirm it.

## User Model

Add the `CanConfirm` trait and `Confirmable` interface to your User model.

```php
use Zotel\Wallet\Interfaces\Confirmable;
use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Traits\CanConfirm;
use Zotel\Wallet\Traits\HasWallet;

class UserConfirm extends Model implements Wallet, Confirmable
{
    use HasWallet, CanConfirm;
}
```

> You can only confirm the transaction with the wallet you paid with.

## To confirmation

### Example:

Sometimes you need to create an operation and confirm its field. 
That is what this trey does.

```php
$user->balance; // 0
$transaction = $user->deposit(100, null, false); // not confirm
$transaction->confirmed; // bool(false)
$user->balance; // 0

$user->confirm($transaction); // bool(true)
$transaction->confirmed; // bool(true)

$user->balance; // 100 
```

It's simple!
