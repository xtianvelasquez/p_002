# Contributing

## Application structure

- `public/index.php` configures the session, error handling, and application routes.
- `core/` contains routing, shared base classes, database access, and helpers.
- `modules/` contains authentication, user, appointment, and layout code.
- `database/migrations/` contains ordered PostgreSQL changes for existing databases.
- `docker/` and `compose.yaml` define the local Apache/PHP and PostgreSQL environment.

## Local development

Use the Docker setup in the README, or copy `.env.example` to `.env`, configure PostgreSQL, initialize a new database with `php import_db.php`, and serve only the `public/` directory. Keep secrets, uploaded files, and database exports out of commits.

## Data and security

- Use PDO prepared statements for database values and HTML-escape rendered values.
- Require CSRF validation for every state-changing request.
- Enforce ownership checks in database queries for user-owned records.
- Validate upload contents, external URLs, and values used for scheduling.
- Do not add real user data or credentials to fixtures, migrations, or screenshots.
- `php import_db.php --reset` drops and rebuilds application tables. Use it only with disposable data.

## Changes and verification

Place schema changes in `database/migrations/` and document any manual rollout steps. The security suite modifies database rows; follow the disposable `_test` database instructions in the README before running it.
