# Vedha Laravel Backend — Complete, Ready-to-Deploy

This is the **full Laravel 11 project** (all source files except `vendor/`,
which Railway's build step installs automatically via Composer). It replaces
the earlier partial upload that was missing `public/index.php`, `artisan`,
`bootstrap/`, and `config/` — which is why Railway was serving FrankenPHP's
default "It works!" page instead of your app.

## Deploy steps (Railway)

1. **Delete everything in your GitHub repo** (`vedha_laravel_full`) — select
   all files/folders on GitHub and delete them, or just delete and recreate
   the repo.
2. **Upload every file in this zip** to that repo (drag the whole extracted
   folder's contents into GitHub's "Add file → Upload files").
3. Commit changes. Railway will auto-redeploy.
4. In Railway → your service → **Variables**, make sure these exist:
   ```
   APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
   APP_URL=https://vedhaaudiobook.up.railway.app
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
   RAILPACK_PHP_ROOT_DIR=/app/public
   ```
   (Generate `APP_KEY` at https://randomkeygen.com/laravel-key if you don't
   have one yet.)
5. Once redeployed and green, you still need to run migrations + seed the
   first admin login. Railway's free plan has no SSH, so add this as a
   **one-time deploy command** instead:
   - Go to **Settings → Deploy → Custom Start Command** and temporarily set:
     ```
     php artisan migrate --force && php artisan storage:link && php -d upload_max_filesize=600M -d post_max_size=620M -d max_execution_time=300 -d max_input_time=300 -S 0.0.0.0:$PORT -t public
     ```
   - Keep this command as the Railway Custom Start Command. It runs pending
     migrations, creates the storage link, and starts PHP with upload limits
     explicitly set. The `php -d` flags matter here: `.user.ini` is not reliably
     applied by PHP's built-in server, so large chapter MP3 uploads can otherwise
     be dropped before Laravel receives them.

## After deploy

Visit `https://vedhaaudiobook.up.railway.app/login`

Default login (from AdminSeeder):
- Email: `admin@vedha.com`
- Password: `password`

**Change this password immediately** from Settings → Password once logged in.

## Why the earlier upload failed

A Laravel project needs these to actually run, none of which were in the
first upload:
- `public/index.php` — the single entry point every request goes through
- `artisan` — CLI needed for migrations/seeding
- `bootstrap/app.php` — wires up routing, middleware, exceptions
- `config/*.php` — database, auth guards, mail, session, filesystem config
- Default Laravel migrations (`users`, `cache`, `jobs` tables) — required
  internally even though your app doesn't use the `users` table directly

This zip includes all of that, plus every custom file (migrations, models,
controllers, blade views, routes) built earlier for the admin panel.
## Book covers and uploaded media (important)

- This version normalizes legacy `image_path` values (`books/covers/...`, `storage/books/covers/...`, or an absolute URL) when building cover URLs.
- The admin list now shows a safe initial-letter placeholder if a cover URL returns 404, and Visibility/Premium/Featured switches verify the server response and revert visibly if saving fails.
- `php artisan storage:link` exposes the public storage disk, but **it cannot restore image/audio files that are already missing from the server**. If old cover files are absent from `storage/app/public/books/covers/`, restore them from a backup or re-upload them.
- Railway's local filesystem may not persist uploads across redeploys unless a persistent volume/object storage is configured. Do not delete the existing database or rerun seeders as a way to fix missing covers.

