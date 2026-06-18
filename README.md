# CLMIS - Centralised Land Management Information System

Pure PHP 8.2 MVC application for managing land plots, owners, ownership transactions, documents, reports, users, and audit logs. It is built for Render hosting with Supabase PostgreSQL and Brevo SMTP.

## Stack

- PHP 8.2 with PDO PostgreSQL
- Supabase PostgreSQL
- PHPMailer through Brevo SMTP
- HTML, CSS, vanilla JavaScript, Chart.js CDN
- Docker deployment on Render

## Environment Variables

| Key | Description | Example |
| --- | --- | --- |
| DB_HOST | Supabase database host | db.xxxx.supabase.co |
| DB_PORT | PostgreSQL port | 5432 |
| DB_NAME | Database name | postgres |
| DB_USER | Database user | postgres |
| DB_PASS | Database password | secret |
| APP_SECRET | Random application secret | 64-character-string |
| APP_ENV | App environment | production |
| APP_URL | Render app URL | https://clmis.onrender.com |
| MAIL_HOST | Brevo SMTP host | smtp-relay.brevo.com |
| MAIL_PORT | Brevo SMTP port | 587 |
| MAIL_USERNAME | Brevo login email | user@example.com |
| MAIL_PASSWORD | Brevo SMTP key | secret |
| MAIL_FROM | Sender email | noreply@example.com |
| MAIL_FROM_NAME | Sender name | CLMIS System |

## Supabase Setup

Create a Supabase project, open SQL Editor, and run `database/migrations/001_create_users.sql` through `007_create_reports_view.sql` in order. Then run `database/seeds/admin_seed.sql` (or `database/seeds/seed_all.sql` for a full dataset with sample plots, owners, and transactions).

Default seeded login:

- Email: `admin@clmis.gov`
- Password: `Admin@1234!` (applies to all seeded users)

Change this password immediately after first login.

Seeded users (password: `Admin@1234!` for all):

| Email             | Role       |
|-------------------|------------|
| admin@clmis.gov   | superadmin |
| ibrahim@clmis.gov | admin      |
| chioma@clmis.gov  | officer    |
| femi@clmis.gov    | viewer     |

## Render Deployment

Push this repository to GitHub. In Render, create a new Web Service, choose Docker runtime, connect the repository, and set the health check path to `/api/ping`. Add the environment variables above using your Supabase and Brevo credentials. Render will build with the included Dockerfile and run Composer inside the container.

## Development Notes

No local database is required. For local PHP syntax checks, use PHP if it is already available on your machine. Do not commit real `.env` credentials or uploaded files.

## Contribution Guide

Keep SQL queries parameterised, preserve role checks, add audit entries for data-changing actions, and keep schema changes in numbered migration files.
