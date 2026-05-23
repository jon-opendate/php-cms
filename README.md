# PHP CMS

A small test CMS built with PHP, Apache, and PostgreSQL.

## Run it

```sh
docker compose up --build
```

Open <http://localhost:8080>.

The app service runs PHP 8.3 with Apache. The database service runs PostgreSQL
16 and initializes a `posts` table with one sample post.

## Database settings

The compose file uses these defaults:

- Database: `cms`
- User: `cms`
- Password: `cms_password`
- Host from the app container: `db`

Postgres data is stored in the named Docker volume `postgres_data`.
