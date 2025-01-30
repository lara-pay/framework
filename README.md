# Laravel Pay

Laravel Pay is a lightweight package for Laravel that allows you to easily integrate payment gateways into your application.

## Requirements

- PHP 8.2 or higher
- Laravel 11.0 or higher

## Installation

To install the package, use Composer:

```bash
composer require laravelpay/framework
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

public function purchaseMacbook()
{
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

## Support

If you have any questions or issues, please create a new issue in the project repository on GitHub.

## License

This project is licensed under the MIT License. See the [LICENSE](https://github.com/laravelpay/framework/blob/main/LICENSE) file for details.
