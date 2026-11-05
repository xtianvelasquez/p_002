# Contributor notes

## Application structure

- `public/index.php` configures the session, installs the shared error handler, and registers routes.
- `core/` contains the router, controller/model base classes, database connection, and shared helpers.
- `modules/<Domain>/Controllers` handles requests; `Models` owns SQL; `Views` renders HTML.
- `modules/Layout/main.php` is the shared page shell. Keep new pages responsive with the existing Bootstrap and `public/css/app.css` styles.
- `p_002_postgres.sql` is the PostgreSQL schema and development seed. `p_002.sql` is a legacy MariaDB dump and is not used by the app.

## Security and data handling

- Use PDO prepared statements for database values and HTML-escape values when rendering attributes or text.
- Require CSRF validation for every state-changing POST, including account actions.
- Keep destructive changes behind explicit operator intent. `php import_db.php --reset` drops and rebuilds application tables.
- Store secrets in an ignored `.env`; never commit credentials or production data.
- Validate external URLs, upload contents and dimensions, and all values that affect scheduling.

## Local setup

Copy `.env.example` to `.env`, configure a development database, then run `php import_db.php` against a new or empty database. Serve only the `public/` directory. Existing database changes belong in `database/migrations/`.
