# Maison De Mystere UAE Perfume Store

Production-oriented Laravel e-commerce application for a UAE luxury perfume boutique. The app uses Laravel 12 on PHP 8.2, Blade, Breeze auth, Livewire, Tailwind CSS, Filament Admin, Spatie roles/permissions, queued mail notifications, and a modular payment gateway layer.

## Features

- Localized storefront at `/en` and `/ar` with RTL Arabic layout.
- AED currency, 5% UAE VAT, VAT invoice breakdown, optional TRN field.
- UAE emirate delivery rules, free shipping thresholds, COD fee, delivery slots.
- Product catalog with brands, categories, variants, galleries, fragrance notes, reviews, wishlist, compare, search and filters.
- Cart, coupons, guest/authenticated checkout, saved addresses, order confirmation.
- Stripe Checkout gateway with webhook signature verification and idempotent callback logs.
- Tap Payments gateway implementation behind the same interface.
- Filament admin panel for products, categories, brands, orders, coupons, reviews, delivery, settings, customers, payment logs, banners, pages, and newsletter subscribers.
- Spatie roles/permissions, admin activity logging package, policies, throttled checkout, secure uploads, queued notifications.
- SEO basics: localized URLs, meta tags, canonical, Open Graph, product schema, sitemap, robots.
- Light/dark mode with persisted user preference and system preference fallback.

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
php artisan serve
```

For this workspace, SQLite is already configured in `.env` for quick local verification. For production, use the MySQL settings in `.env.example`.

Admin login:

```text
URL: /admin
Email: admin@maisondemystere.ae
Password: password
```

Sample customer:

```text
Email: customer@example.com
Password: password
```

## Payments

Payment implementations live in:

```text
app/Services/Payments/PaymentGatewayInterface.php
app/Services/Payments/StripePaymentGateway.php
app/Services/Payments/TapPaymentGateway.php
app/Services/Payments/PaymentManager.php
```

Set `PAYMENT_GATEWAY=stripe` or `PAYMENT_GATEWAY=tap`. Stripe uses hosted Checkout Sessions and verifies webhooks using `STRIPE_WEBHOOK_SECRET`. Without a local Stripe secret, the gateway creates a local test payment record and redirects to order confirmation so checkout remains testable.

Webhook endpoint:

```text
POST /payments/webhook
```

## Queues and Mail

Order, payment, delivery, low stock, review, and newsletter notifications are queued. Run a worker in production:

```bash
php artisan queue:work --tries=3
```

Configure SMTP in `.env`. The local `.env` uses `MAIL_MAILER=log`.

## Testing

```bash
php artisan test
```

Current coverage includes authentication, profile management, product browsing, cart/checkout, UAE VAT calculation, Dubai free shipping, idempotent payment webhook handling, and admin authorization.

## Deployment Notes

- Use PHP 8.2+ with required extensions for Laravel, SQLite/MySQL, fileinfo, mbstring, openssl, pdo, tokenizer, xml, ctype, json, curl.
- Point the web server document root to `public/`.
- Use MySQL or PostgreSQL in production; keep Redis for cache/queues if available.
- Run `php artisan config:cache route:cache view:cache` during deploy.
- Run `php artisan migrate --force`.
- Run `php artisan storage:link`.
- Keep `APP_DEBUG=false`.
- Configure payment webhook URLs in the Stripe/Tap dashboard.
- Use HTTPS for checkout and admin.
- Set strict file upload limits and back product images with a durable filesystem disk such as S3-compatible storage.
