# Withdraw

When there is enough money in the account, you can transfer/withdraw 
it or buy something in the system.

Since the currency is virtual, you can buy any services on your website. 
For example, priority in search results.

## User Model

<!--@include: ../../_include/models/user_simple.md -->

## Make a Withdraw

Find user:

```php
$user = User::first(); 
```

As the user uses `HasWallet`, he will have `balance` property. 
Check the user's balance.

```php
$user->balance; // 100
$user->balanceInt; // 100
```

The balance is not empty, so you can withdraw funds.

```php
$user->withdraw(10); 
$user->balance; // 90
$user->balanceInt; // 90
```

It's simple!

## Force Withdraw

Forced withdrawal is necessary for those cases when 
the user has no funds. For example, a fine for spam.

```php
$user->balance; // 100
$user->balanceInt; // 100
$user->forceWithdraw(101);
$user->balance; // -1
$user->balanceInt; // -1
```

## And what will happen if the money is not enough?

There can be two situations:

- The user's balance is zero, then we get an error
`Zotel\Wallet\Exceptions\BalanceIsEmpty`
- If the balance is greater than zero, but it is not enough
`Zotel\Wallet\Exceptions\InsufficientFunds`
