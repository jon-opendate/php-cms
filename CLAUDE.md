# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- `docker compose up --build` — build and run the app at <http://localhost:8080>. The `db` service must reach `pg_isready` before `app` starts (healthcheck gates `depends_on`).
- `docker compose down -v` — stop and remove the `postgres_data` and `redis_data` named volumes. Required to re-run the SQL in `db/init/` (Postgres only seeds an empty data directory) and to clear cached view counters.
- `docker compose exec db psql -U cms -d cms` — open a psql shell against the running DB.
- `docker compose exec redis redis-cli` — open a redis-cli shell. Counters live under `post:views:{id}`.
- `docker compose exec app php -l /var/www/html/index.php` — lint a PHP file inside the container (no composer/test tooling is configured).

There is no test suite, linter config, or build step beyond Docker. To verify changes, hit routes with curl after bringing the stack up (e.g. `curl -s http://localhost:8080/`). If host ports `5432`/`6379` are busy, drop a gitignored `docker-compose.override.yml` that does `services.db.ports: !reset []` and `services.redis.ports: !reset []` — the app still talks to both over the Docker network.

## Architecture

PHP 8.3 + Apache + mod_php in front of PostgreSQL 16 (content) and Redis 7 (per-post view counters). Both are required dependencies — `redis()` throws `RuntimeException` if it can't connect, and the page errors out. The site has a **public face at `/`** and an **admin face at `/admin`** — no auth, this is intentional for the test app.

### Front controller + routing

Apache has `mod_rewrite` enabled (Dockerfile) and `AllowOverride All` on `/var/www/html` (set by `apache/cms.conf` copied into `/etc/apache2/conf-enabled/`). `public/.htaccess` rewrites any request that isn't a real file or directory to `index.php`, so `styles.css` and other static files still serve directly while everything else hits the front controller.

`public/index.php` is the only router. It splits `REQUEST_URI`'s path into segments and dispatches:

- `/` → `view('home', ['posts' => published_posts()])`
- `/{slug}` → `find_published_post_by_slug($slug)`; renders `views/post.php` or 404s
- `/admin*` → delegates to `admin_dispatch($remainingSegments, $method)` in `src/admin.php`

`admin_dispatch` handles POSTs to `/admin` (create), `/admin/edit/{id}` (update), and `/admin/delete/{id}` (delete), then redirects to `/admin`. GETs render `views/admin.php` with the full post list and, when the path is `/admin/edit/{id}`, the post being edited.

### Data layer

- `src/db.php` — `db()` returns a memoized PDO singleton. Connection params come from `DB_HOST`/`DB_PORT`/`DB_NAME`/`DB_USER`/`DB_PASS` env vars (set by `docker-compose.yml`), with localhost/`cms`/`cms_password` fallbacks.
- `src/redis.php` — `redis()` returns a memoized `\Redis` (from the `phpredis` extension). Connection params come from `REDIS_HOST`/`REDIS_PORT` env vars, with `localhost`/`6379` fallbacks. Connection failure throws `RuntimeException`.
- `src/posts.php` — plain functions over the `posts` table plus the Redis view counters. Two pairs matter on the SQL side: `all_posts()` / `find_post($id)` are admin-only (return all statuses); `published_posts()` / `find_published_post_by_slug($slug)` are the public-side queries and filter to `status = 'published'`. Drafts are therefore invisible from `/{slug}` even though the row exists. `save_post()` doubles as create/update based on whether `$id` is null. The Redis side: `record_post_view(int)` `INCR`s `post:views:{id}` and is called once per `GET /{slug}`; `post_view_count(int)` and `post_view_counts(int[])` read counters (the latter uses `MGET` for the home/admin lists); `forget_post_view_count(int)` is called from `admin_dispatch` after a delete so the counter doesn't outlive the post.

### Rendering

`src/render.php` exposes `e()` (HTML-escape) and `view($template, $vars)` (extracts vars and `require`s `src/views/{$template}.php`). Every view template is a self-contained HTML document — there is no shared layout file. The `e()` helper must wrap any user-controlled value before it hits the page.

### Schema seeding

`db/init/*.sql` is mounted at `/docker-entrypoint-initdb.d` and run by Postgres **only when the data directory is empty**. Schema changes after first boot require dropping the `postgres_data` volume (`docker compose down -v`) or writing a real migration. The `posts.slug` column has a `UNIQUE` constraint; `save_post` does not catch the resulting `PDOException`, so duplicate slugs surface as the red error banner in `views/admin.php` (rendered from the caught `Throwable` in `admin_dispatch`).
