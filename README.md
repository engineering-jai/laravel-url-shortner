# URL Shortener

Multi-tenant URL shortener built with Laravel 11. Companies have users with roles (SuperAdmin, Admin, Member). Admins and Members create short URLs; access to the list is scoped by role. Public links redirect at `/s/{code}`.

## What you need

- PHP 8.2+
- Composer
- MySQL
- Node & npm (for Breeze/Vite assets)

## Setup

Clone the repo, then:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Create a MySQL database and set `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`.

```bash
php artisan migrate
php artisan db:seed
```

For local dev with the built-in UI:

```bash
npm install
npm run dev
```

In another terminal:

```bash
php artisan serve
```

Open http://localhost:8000.

## Seeded account

After `db:seed` you get one user:

- **Email:** superadmin@example.com  
- **Password:** password  

That user is a SuperAdmin: can create companies, send invitations, and see all short URLs. They cannot create short URLs. To get Admins or Members, use the Invite flow from the dashboard (SuperAdmin creates a company, then invites an Admin; that Admin can then invite others).

## Accepting an invitation

When someone is invited, they can accept by opening this URL in a browser (replace `YOUR_TOKEN_HERE` with the actual token):

```
http://localhost:8000/invitations/accept/YOUR_TOKEN_HERE
```

To get the token for a pending invitation you can use tinker:

```bash
php artisan tinker
```

Then run:

```php
\App\Models\Invitation::whereNull('accepted_at')->where('expires_at', '>', now())->first()?->token
```

Copy the token and visit `http://localhost:8000/invitations/accept/<token>`. On that page the user sets their name and password and is then logged in. If mail is configured, the invitation email also contains the accept link.

## Tests

```bash
php artisan test
```

Or only the short URL tests:

```bash
php artisan test tests/Feature/ShortUrlCreationTest.php
```


## Behaviour in short

- **SuperAdmin:** Manages companies and invitations; sees all short URLs (read-only).
- **Admin:** Invites Admins/Members to their company; creates short URLs; sees only their company’s short URLs.
- **Member:** Creates short URLs; sees only their own.
- **Public:** `/s/{short_code}` redirects to the original URL (no auth).

Registration is disabled; new users join via invitation and set their password on accept.
