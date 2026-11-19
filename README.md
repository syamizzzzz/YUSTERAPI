# YUSTERAPI Customer Management System

Laravel 13 application with a public YUSTERAPI landing page and a private staff dashboard for customer profiles, health history, monthly bookings, treatment packages, payments and notes. The dashboard opens to January 2027 and can be filtered by month. Customer IDs are generated as `YT00001`, `YT00002`, and so on.

## Start locally

Requirements: PHP 8.3 or newer, Composer, and the SQLite PHP extension.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item database/database.sqlite -ItemType File -Force
php artisan migrate
php artisan staff:create admin@example.com "Your Name"
php artisan serve
```

The `staff:create` command prompts privately for a password of at least 12 characters. Open `http://127.0.0.1:8000` for the public landing page; staff sign in at `http://127.0.0.1:8000/login` and use the dashboard at `/admin`. In this workspace, dependencies, `.env`, the application key, and the SQLite database are already prepared, so only `staff:create` and `serve` are needed.

No npm build is needed; the interface stylesheet is served from `public/css/yusterapi.css`.

## Supabase Postgres

This project can migrate its current SQLite records to a new Supabase Postgres database without resetting staff passwords. Keep the existing `.env` and SQLite file as a backup.

1. Create a Supabase project on the Free plan.
2. Copy the **Session pooler** connection string (port `5432`) from the project's **Connect** dialog. Add it to the local `.env` as `SUPABASE_DB_URL=...`. URL-encode special characters in the database password.
3. Run `php artisan yusterapi:move-to-supabase`. The command creates the Laravel tables, enables row level security, blocks Supabase API roles from the private tables, and copies the existing users, customers, packages, bookings, payments, notes and appointment requests. It stops if the target tables already contain data.
4. Once all table counts match, set `DB_CONNECTION=pgsql` and `DB_URL` to the same Session pooler URL in `.env`, then run `php artisan config:clear` and restart `php artisan serve`.

Keep the Postgres connection string in `.env` only; never put it in browser JavaScript or commit it to source control. The application uses Laravel's server-side database connection, not the public Supabase Data API.

## Data and access

- The landing page is public. Its contact form stores appointment requests for staff to review at `/admin/permintaan`; submitting the form does not confirm a booking or take payment.
- All customer, health, booking and payment screens require staff login. Public registration is disabled.
- Bookings belong to a customer and can optionally use a treatment package. Payments belong to a booking. Notes belong to a customer and can optionally reference one of that customer's bookings.
- Deleting a customer also deletes their bookings, payments and notes. Deleting a booking deletes its payments and unlinks related notes. The interface asks for confirmation before these actions.
- Use `php artisan test` to run the feature tests. For production, set `APP_ENV=production`, `APP_DEBUG=false`, use HTTPS, configure backups and use a supported production database.
