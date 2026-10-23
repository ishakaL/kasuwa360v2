# Kasuwa360 MVP

Kasuwa360 is a native PHP marketplace MVP for local vendors, buyers, and administrators in Nigeria. The project uses plain PHP, PDO, MySQL-compatible SQL, HTML, CSS, and vanilla JavaScript.

## Quick start

1. Place this project in `C:\xampp\htdocs\LMV`.
2. Ensure Apache is running in XAMPP.
3. Copy `.env.example` to `.env` and adjust values if needed.
4. Create a MySQL database named `kasuwa360` in phpMyAdmin. The application applies `database/schema.sql` and `database/seed.sql` when it first connects.
5. Set the database credentials in `.env`.
6. Visit `http://localhost/LMV` in the browser.
7. Set `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env` before the first application request; the application uses these values to create the initial administrator.

## Project structure

- `app/` – Controllers, models, services, validators, and core utilities
- `config/` – App configuration
- `database/` – Schema and seed SQL files
- `public/` – Public entry point and static assets
- `resources/views/` – PHP view templates
- `storage/uploads/` – Uploaded media
- `tests/` – Smoke/check scripts

## Database

The project uses MySQL/MariaDB by default with `database/schema.sql` and `database/seed.sql`. SQLite is available for isolated functional tests using `database/schema.sqlite.sql` and `database/seed.sqlite.sql`. The database driver is selected by `DB_DRIVER`; connection errors are reported instead of silently changing database engines.

## Environment

Example values in `.env.example`:

```env
APP_ENV=development
APP_URL=http://localhost/LMV
DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=kasuwa360
DB_USERNAME=root
DB_PASSWORD=
UPLOAD_MAX_SIZE=2097152
ADMIN_EMAIL=admin@kasuwa360.local
ADMIN_PASSWORD=replace-with-a-strong-local-password
```

The database account must have permission to create tables and indexes in the selected database. For local testing, `tests/functional.php` forces an isolated SQLite database and does not modify the configured MySQL database.

## Features implemented

- Public marketplace homepage and listing search/filter pages
- Vendor registration and login
- Vendor dashboard and shop setup
- Listing creation, editing, and deletion
- Admin dashboard with user, shop, listing, and category flows
- Upload validation and secure file handling
- CSRF protection and server-side role checks

## Notes

- No project logo asset was found in the supplied workspace. The app uses brand-inspired colors and a placeholder logo treatment until a proper asset is added.
- The application is designed as an MVP and should be hardened further before production deployment.





git init
git add .
git commit -m "first commit"
git branch -M main
git remote add origin https://github.com/ishakaL/kasuwa360v2.git
git push -u origin main