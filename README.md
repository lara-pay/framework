# Laravel Pay

Laravel Pay is a lightweight package for Laravel that allows you to easily integrate payment gateways into your application.

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or higher

## Supported Gateways

| Gateway | Install Command |
| ------------- | ------------- |
| [PayPal](https://github.com/laravelpay/gateway-paypal) | php artisan gateway:install laravelpay/gateway-paypal |
| [PayPal IPN](https://github.com/laravelpay/gateway-paypal-ipn) | php artisan gateway:install laravelpay/gateway-paypal-ipn |
| [Stripe](https://github.com/laravelpay/gateway-stripe) | php artisan gateway:install laravelpay/gateway-stripe |
| [Mollie](https://github.com/laravelpay/gateway-mollie) | php artisan gateway:install laravelpay/gateway-mollie |
| [Tebex](https://github.com/laravelpay/gateway-tebex) | php artisan gateway:install laravelpay/gateway-tebex |
| [BitPave](https://github.com/laravelpay/gateway-bitpave) | php artisan gateway:install laravelpay/gateway-bitpave |
| [PayByLink](https://github.com/laravelpay/gateway-bitpave) | php artisan gateway:install laravelpay/gateway-paybylink |

## Subscription Gateways

| Gateway | Install Command |
| ------------- | ------------- |
| [PayPal Subscriptions](https://github.com/laravelpay/subscriptions-paypal) | php artisan gateway:install laravelpay/subscriptions-paypal |
| [Stripe Subscriptions](https://github.com/laravelpay/subscriptions-stripe) | php artisan gateway:install laravelpay/subscriptions-stripe |

## Installation
To install the package, use Composer:

```bash
composer require laravelpay/foundation dev-main
```

## Installing Gateways

Laravel Pay allows you to install gateways based on your needs. The command below allows you to install one of our default gateways.

```bash
php artisan gateway:install
```

To install custom gateways, you may pass it in the argument and the GitHub owner/repo i.e `php artisan gateway:install laravelpay/gateway-paypal-ipn`

## Setting up Gateways

You can create multiple configurations for each gateway. Below is an example of how you might set up the PayPal gateway.

```bash
php artisan gateway:setup
```

## Usage

Below is an example of how you might use the package in your Laravel application.

```php
use LaraPay\Framework\Payment;

Route::get('/macbook/purchase', function () {
    $payment = Payment::create([
        'amount' => 2000,
        'currency' => 'USD',
        'description' => 'Macbook Pro',
    ]);
    
    return $payment->payWith('paypal');
}
```

In the payWith() method, pass the id or alias of the gateway.

## Creating temporary payment links
This package comes with a built-in method that allows you to generate temporary links that redirect the user to make the payment.

```php
use LaraPay\Framework\Payment;

$payment = Payment::create([
    'amount' => 2000,
    'currency' => 'USD',
    'description' => 'Macbook Pro',
]);

// laravel-pay generates a temporary payment link for the gateway
$link = $payment->generateLinkForGateway('paypal'); // http://laravel.app/payments/pay/awFlSUrsmKsoVtLHQBzLziFFnqoSsXt6

return redirect($link);
```

## Executing code when payment is completed
After a user completes a payment, you may want to execute some code to fulfill the users order. This can be done using Payment Handlers.

1. First create a new php file in app/PaymentHandlers, in our case the file will be called MacbookPaymentHandler.php
```php
namespace App\PaymentHandlers;

LaraPay\Framework\Interfaces\PaymentHandler;

class MacbookPaymentHandler extends PaymentHandler
{
    public function onPaymentCompleted($payment)
    {
        // execute code when payment is completed
    }
}
```

2. Generate the payment and pass the handler class

```php
use LaraPay\Framework\Payment;
use App\PaymentHandlers\MacbookPaymentHandler;

$payment = Payment::create([
    'user_id' => 1,
    'amount' => 2000,
    'currency' => 'USD',
    'description' => 'Macbook Pro',
    'handler' => MacbookPaymentHandler::class,
]);
```

## Payments for users
You may want to attach specific payments to specific users. You can do so by passing the user id when the payment is created

```php
use LaraPay\Framework\Payment;

$payment = Payment::create([
    'user_id' => 1,
    'amount' => 2000,
    'currency' => 'USD',
    'description' => 'Macbook Pro',
]);
```

The user can be retrieved later when the payment is completed using `$payment->user`

## Passing custom data
Custom data can be passed when the payment is being created

```php
use LaraPay\Framework\Payment;

$payment = Payment::create([
    'amount' => 2000,
    'currency' => 'USD',
    'description' => 'Macbook Pro',
    'data' => [
        'order_id' => 1, 
    ],
]);
```

## Custom success and cancel urls
Define the URL where the user should be redirected to in the event that the payment gets cancelled or is successfully completed.
```php
use LaraPay\Framework\Payment;

$payment = Payment::create([
    'amount' => 2000,
    'currency' => 'USD',
    'description' => 'Macbook Pro',
    'success_url' => route('your-success-route'),
    'cancel_url' => route('your-cancel-route'),
]);
```

## Subscriptions
This package includes support for some subscription based gateways. This system works similarly to normal payments.

```php
use LaraPay\Framework\Subscription;

Route::get('/plans/basic/subscribe', function () {
    $subscription = Subscription::create([
        'name' => 'Basic Plan',
        'amount' => 2000,
        'currency' => 'USD',
        'frequency' => 30, // The frequency (in days) on which the subscription will be charged. 7 is weekly, 30 monthly, 365 yearly 
    ]);
    
    return $subscription->subscribeWith('paypal');
}
```

## Support

If you have any questions or issues, please create a new issue in the project repository on GitHub.

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/laravelpay/framework/blob/main/LICENSE) file for details.
