# Laboratory Exercise 06

Bon Josh Paolo D. Ramirez · MCC2023-1110 · BS Information Technology · 3rd Year · 3F2

## Application

React frontend → HTTP JSON API → LavaLust ProductModel → Aiven MySQL.
The application supports login, listing products, adding, editing, deleting, and logout.
PHP prices and whole-number quantities are validated on the server. The browser connects only to the API.

## API endpoints

| Method | Path | Purpose |
| --- | --- | --- |
| GET | /api/health | Public service status |
| POST | /api/login | Sign in and obtain access/refresh tokens |
| POST | /api/refresh | Rotate an authenticated session |
| POST | /api/logout | Revoke the session |
| GET | /api/me | Current authenticated user |
| GET | /api/products | List products |
| GET | /api/products/{id} | Find a product |
| POST | /api/products | Add a product |
| PUT | /api/products/{id} | Replace product fields |
| PATCH | /api/products/{id} | Update selected fields |
| DELETE | /api/products/{id} | Delete a product |

Product endpoints require `Authorization: Bearer <access_token>`.
JSON bodies use `Content-Type: application/json`.
Product fields: `product_name`, `description`, `price`, `quantity`.
Responses use LavaLust's Api class through LabApi. Validation returns HTTP 422, missing products HTTP 404,
and unauthenticated requests HTTP 401. Refresh tokens are hashed in the database; logout invalidates both tokens.

## Migrations

The installed framework loads custom commands from `app/commands/` (plural).
`MigrationController` routes are accessible only through the CLI, protecting the production database from HTTP rollbacks.

```sh
php lava migration run
php lava migration status
php lava migration create-migration your_new_table
```

On a disposable development database only:

```sh
php lava migration rollback --confirm
php lava migration rollback-all --confirm
php lava migration refresh --confirm
```

Migrations create `migrations`, `users`, `refresh_tokens`, and `products`.
Additional migrations extend older Lab 4 users without dropping data and seed the admin with a hashed `AUTH_PASSWORD`.
Existing product records are preserved. The Render startup script runs only pending migrations.

## Configuration

Backend: `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`, `DB_DATABASE`, `DB_SSL_CA`,
`APP_KEY` (at least 32 random characters), `AUTH_USERNAME`, `AUTH_PASSWORD`, and `FRONTEND_URL`.
Required API keys: `JWT_SECRET` and `REFRESH_TOKEN_KEY`, each at least 32 random characters and different.
Run `php lava jwt:generate` locally to generate and save independent 64-character keys in the ignored `.env` file.
Set the same two values privately on the Render backend; never put them in the React environment or GitHub.
The command preserves existing values unless `--force` is supplied; avoid `--show` in shared terminals.
Replacing either key signs out existing API sessions. The updated framework verifies access/refresh token types
and reads user roles from the database. LabApi also checks active users and revocable session records.
`FRONTEND_URL` accepts comma-separated trusted frontend origins.
Frontend: public `VITE_API_URL` points to the backend `/api` URL. It must never contain database credentials.

## Verification

```sh
php tests/lab6_product_validation_test.php
php tests/lab6_api_secrets_test.php
python tests/lab6_jwt_cli_test.py --php /path/to/php
python tests/lab6_api_integration.py --php /path/to/php
cd frontend
npm ci
npm run build
```

The integration test uses an isolated SQLite fixture and sends actual HTTP requests to PHP.
It checks protected GET/POST/PUT/PATCH/DELETE, login, validation, CORS preflight, refresh rotation,
and immediate session revocation after logout. It never changes the live Aiven database.
The JWT CLI test uses a disposable .env. The API security test checks missing, weak, or identical secrets,
access/refresh token separation, expiry, and database-derived roles.
An autoloaded `app/helpers/api_helper.php` supplies the global CORS helper required by the uploaded API
library in this older framework checkout, preserving the restricted React origin and OPTIONS responses.

## Submission checklist

- Backend GitHub: https://github.com/ramirez-bonjoshpaolo-hub/ramirez-bonjoshpaolo-lavalust
- React GitHub: https://github.com/ramirez-bonjoshpaolo-hub/ramirez-bonjoshpaolo-lavalust-react
- Backend API: https://ramirez-bonjoshpaolo-lavalust-od8y.onrender.com/api
- Frontend URL: https://ramirez-bonjoshpaolo-lavalust-react.onrender.com
- Screenshots: login, product list, add, edit, delete confirmation, Aiven tables.
- Demonstration: login → list → add → edit → delete → logout → protected route rejected.

## Current deployment status

Both the React frontend and LavaLust backend are live on Render. The Aiven MySQL service is running;
all six migrations (000 through 005) are applied. The earlier DNS failure was resolved after powering on Aiven.

Live verification passed on 2026-10-04: React login, product listing, add, edit, delete confirmation, and logout;
authenticated GET/POST/PUT/PATCH/DELETE, CORS preflight, input validation, refresh rotation, and logout revocation.
A uniquely named temporary test product was created, edited, and deleted. All seven pre-existing products
were verified unchanged. Test login sessions were revoked afterward.

Live screenshots are saved locally in `output/lab6-live-screenshots` and `output/lab6-live-screenshots.zip`.
They show actual Render pages connected to Aiven. The Aiven Console table screenshot must be captured separately.
Screenshots in `output/lab6-local-screenshots` are isolated test fixtures, not live Aiven records.

Reference: https://lavalust.netlify.app/docs/libraries/api.html
