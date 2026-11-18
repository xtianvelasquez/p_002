# More Than Blue

More Than Blue is a responsive appointment scheduling application built with PHP 8.1+, PostgreSQL, and Bootstrap. It includes account and profile management, professional discovery, configurable availability, appointment requests, and a calendar view.

## Highlights

- Modular PHP MVC structure with a custom router
- PostgreSQL persistence through PDO prepared statements
- Password hashing, CSRF protection, ownership checks, and upload validation
- Availability-aware appointment booking and request management
- Calendar navigation and downloadable calendar invitations
- Responsive layout for mobile, tablet, and desktop

## Run with Docker

Requirements: Docker Desktop or Docker Engine with the Compose plugin.

1. Copy `.env.example` to `.env` (`Copy-Item .env.example .env` in PowerShell, or `cp .env.example .env` in Bash).
2. Set `DB_PASSWORD` in `.env` to a local development password.
3. Start the application:

   ```sh
   docker compose up --build
   ```

4. Open [http://localhost:8000](http://localhost:8000).

The app container connects to PostgreSQL over the Compose network. PostgreSQL is also available on `localhost:5433` for local database tools. Uploaded profile images are stored in `public/uploads/` and are excluded from Git.

The PostgreSQL schema is applied automatically when the database volume is first created. The schema intentionally contains no demo accounts or appointments. To rebuild the database from scratch, run `docker compose down -v` and start the services again; this permanently removes the local database volume.

## Run without Docker

Requirements: PHP 8.1+ with `pdo_pgsql`, and PostgreSQL 13+.

1. Copy `.env.example` to `.env` and set your local PostgreSQL connection details (`Copy-Item .env.example .env` in PowerShell, or `cp .env.example .env` in Bash).
2. Create an empty database matching `DB_NAME`.
3. Initialize its schema:

   ```sh
   php import_db.php
   ```

4. Serve the `public/` directory:

   ```sh
   php -S localhost:8000 -t public
   ```

Visit [http://localhost:8000](http://localhost:8000). For a production deployment, configure a production web server with `public/` as its document root and provide secrets through the deployment environment.

## Configuration

See `.env.example` for the supported database and application settings. Keep `.env` and all credentials out of version control. `APP_ENV=production` requires a non-empty database password. Optional Google OAuth credentials enable Google sign-in.

## Database changes

`p_002_postgres.sql` is the clean PostgreSQL schema used for a fresh install. It includes destructive `DROP TABLE` statements and should only be run against a new or disposable database. Existing database changes belong in `database/migrations/` and should be reviewed and applied in filename order.

The importer refuses to replace application tables unless explicitly invoked with `php import_db.php --reset`. Reset drops and recreates application tables and deletes their data.

## Tests

The security suite creates and removes database rows. Run it only against a disposable database whose name ends in `_test`, with `APP_ENV=test`:

```powershell
$env:APP_ENV = "test"
$env:DB_NAME = "blue_test"
$env:DB_USER = "postgres"
$env:DB_PASSWORD = "your-test-password"
php test_security.php
```

In Bash, prefix the command with `APP_ENV=test DB_NAME=blue_test DB_USER=postgres DB_PASSWORD=your-test-password` instead.

The test script refuses to run unless both safeguards are set. Do not point it at a database containing data you need.

## Project layout

```text
config/                  Environment and database configuration
core/                    Router, base classes, database access, shared helpers
database/migrations/     Ordered PostgreSQL migrations
modules/                 Authentication, user, appointment, and layout modules
public/                  Web document root and static assets
docker/                  Apache and PHP container configuration
compose.yaml             Local app and PostgreSQL services
p_002_postgres.sql       Fresh-install PostgreSQL schema
```

## License

MIT. See [LICENSE](LICENSE).

Illustration credits are listed in [public/images/ATTRIBUTION.txt](public/images/ATTRIBUTION.txt).
