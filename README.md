# PHP CMS

A small test CMS built with PHP and PostgreSQL.

## Requirements

- PHP 8.1+ with the `pdo_pgsql` extension
- PostgreSQL 12+
- A web server able to serve `public/` as the document root and rewrite
  unmatched requests to `public/index.php` (e.g. via mod_rewrite using the
  bundled `.htaccess`)

## Setup

Create the database and load the schema:

```sh
createdb cms
psql -d cms -f db/init/001_posts.sql
```

Configure the app with these environment variables (defaults in parentheses):

- `DB_HOST` (`localhost`)
- `DB_PORT` (`5432`)
- `DB_NAME` (`cms`)
- `DB_USER` (`cms`)
- `DB_PASS` (`cms_password`)

## Routes

- `/` — public list of published posts
- `/{slug}` — single published post
- `/admin` — editor (create, edit, delete); no authentication
