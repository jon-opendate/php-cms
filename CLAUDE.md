# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

There is no build step, test suite, composer setup, or linter config in this repo. Workflow:

- Serve `public/` with any PHP-capable web server pointed at a running PostgreSQL, with the `DB_*` env vars set (see `src/db.php` for defaults).
- Load or reload the schema: `psql -d cms -f db/init/001_posts.sql`. The seed `INSERT` uses `ON CONFLICT (slug) DO NOTHING`, so re-running it is safe.
- Lint a single file: `php -l path/to/file.php`.
- Verify routes by curling them, e.g. `curl -sS -o /dev/null -w '%{http_code}\n' http://localhost:8080/`.

## Architecture

PHP app served by a front controller, talking to PostgreSQL through PDO. Public site lives at `/`; the editor lives at `/admin` — there is no authentication, by design.

### Front controller + routing

`public/.htaccess` rewrites any request that isn't a real file or directory to `public/index.php`. Real files (`styles.css`) bypass the rewrite and are served directly. The web server must support that rewrite — Apache with `mod_rewrite` and `AllowOverride All` over `public/` works out of the box; on other servers, the same effect can be achieved with a single "fall through to `index.php`" rule.

`public/index.php` is the only router. It splits the request path into segments and dispatches:

- `/` → `view('home', ['posts' => published_posts()])`
- `/{slug}` → `find_published_post_by_slug($slug)`; renders `views/post.php` or 404s
- `/admin*` → delegates to `admin_dispatch($remainingSegments, $method)` in `src/admin.php`

`admin_dispatch` handles POSTs to `/admin` (create), `/admin/edit/{id}` (update), and `/admin/delete/{id}` (delete), then redirects to `/admin`. GETs render `views/admin.php` with the full post list and, when the path is `/admin/edit/{id}`, the post being edited.

### Data layer

- `src/db.php` — `db()` returns a memoized PDO singleton. Connection params come from `DB_HOST`/`DB_PORT`/`DB_NAME`/`DB_USER`/`DB_PASS` env vars, with `localhost`/`cms`/`cms_password` fallbacks.
- `src/posts.php` — plain functions over the `posts` table. Two pairs matter: `all_posts()` / `find_post($id)` are admin-only (return all statuses); `published_posts()` / `find_published_post_by_slug($slug)` are the public-side queries and filter to `status = 'published'`. Drafts are therefore invisible from `/{slug}` even though the row exists. `save_post()` doubles as create/update based on whether `$id` is null.

### Rendering

`src/render.php` exposes `e()` (HTML-escape) and `view($template, $vars)` (extracts vars and `require`s `src/views/{$template}.php`). Every view template is a self-contained HTML document — there is no shared layout file. The `e()` helper must wrap any user-controlled value before it hits the page.

### Schema

`db/init/001_posts.sql` is the canonical schema. It's idempotent (`CREATE TABLE IF NOT EXISTS`, `ON CONFLICT (slug) DO NOTHING`) but schema *changes* must be applied manually — there is no migration tool. The `posts.slug` column has a `UNIQUE` constraint; `save_post` does not catch the resulting `PDOException`, so duplicate slugs surface as the red error banner in `views/admin.php` (rendered from the caught `Throwable` in `admin_dispatch`).
