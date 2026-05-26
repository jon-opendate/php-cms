# PHP CMS

A small test CMS built with PHP, Apache, PostgreSQL, and Redis.

## Run it

```sh
docker compose up --build
```

Open <http://localhost:8080>.

The app service runs PHP 8.3 with Apache and the `pdo_pgsql` + `redis`
extensions. The database service runs PostgreSQL 16 and initializes a
`posts` table with one sample post. The cache service runs Redis 7 and
holds per-post view counters (`post:views:{id}`).

## Database settings

The compose file uses these defaults:

- Database: `cms`
- User: `cms`
- Password: `cms_password`
- Host from the app container: `db`

Postgres data is stored in the named Docker volume `postgres_data`.

## Redis

Redis is required — the app fails to render if it cannot connect.
Defaults from the app container:

- Host: `redis`
- Port: `6379`

Redis data is stored in the named Docker volume `redis_data`.
