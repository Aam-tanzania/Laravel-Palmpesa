Here's a simple and professional `README.md` file for your Laravel PalmPesa payment package:

---

````markdown
# Laravel PalmPesa Payment Package

**PalmPesa** is a lightweight Laravel package that allows you to easily integrate payment by link using the Selcom PalmPesa gateway.

## 🔧 Features

- Generate payment links via API
- Redirect users to PalmPesa checkout
- Check transaction status
- Simple HTML form for testing
- Easy integration with any Laravel project

## 🧰 Requirements

- PHP >= 8.0
- Laravel 8, 9, or 10
- Selcom PalmPesa account

---

## 🚀 Installation

### Via Composer (Packagist - if published)

```bash
composer require yourname/laravel-palmpesa
````

### Or Via GitHub (for development/test)

Add this to your Laravel project's `composer.json`:

```json
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/yourname/laravel-palmpesa"
  }
],
```

Then install:

```bash
composer require yourname/laravel-palmpesa
```

---

## 📦 Package Setup

### 1. Add the Service Provider (if Laravel version < 5.5)

In `config/app.php`:

```php
'providers' => [
    PalmPesa\PalmPesaServiceProvider::class,
],
```

### 2. Publish Config (if available)

```bash
php artisan vendor:publish --tag=palmpesa-config
```

---

## 🧪 Usage

### Payment Form (Pay by USSD PUSH)

```blade
<div class="col-md-6 form-section form-section-right">
    <h3>Payment Form (Pay by USSD PUSH)</h3>
    <form method="POST" action="{{ route('pay.submit') }}">
        @csrf
        <div class="mb-3">
            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="address">Address:</label>
            <input type="text" name="address" id="address" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="postcode">Postcode:</label>
            <input type="text" name="postcode" id="postcode" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="user_id">User ID:</label>
            <input type="number" name="user_id" id="user_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="phone">Phone Number:</label>
            <input type="text" name="phone" id="phone" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="amount">Amount (TSh):</label>
            <input type="number" name="amount" id="amount" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Pay Now</button>
    </form>
</div>
```

---

### Payment Link Form (Example)

```blade
<form method="POST" action="{{ url('/palmpesa/pay-by-link') }}">
    @csrf
    <input type="text" name="user_id" placeholder="User ID">
    <input type="text" name="name" placeholder="Full Name">
    <input type="email" name="email" placeholder="Email">
    <input type="text" name="phone" placeholder="Phone (e.g. 2556...)">
    <input type="number" name="amount" placeholder="Amount">
    <input type="text" name="transaction_id" placeholder="Transaction ID">
    <input type="text" name="address" placeholder="Address">
    <input type="text" name="postcode" placeholder="Postcode">
    <input type="text" name="buyer_uuid" placeholder="Buyer UUID">
    <button type="submit">Get Payment Link</button>
</form>
```



### Redirect Page

```blade
<a href="{{ $paymentLink }}" class="btn btn-primary" target="_blank">Go to Payment</a>

<script>
    setTimeout(function () {
        window.location.href = "{{ $paymentLink }}";
    }, 3000);
</script>
```

---

## 🛠 Configuration (optional)

If the package supports a config file, update your credentials in `config/palmpesa.php`.

---

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

## 👨‍💻 Author

**Anord Amri Mwinuka**
CTO at [PalmPesa](https://palmpesa.co.tz)
[GitHub](https://github.com/Aam-tanzania) • [Email](anoldmwinuka@gmail.com)

```
