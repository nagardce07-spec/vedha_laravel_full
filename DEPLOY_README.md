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
     php artisan migrate --force && php artisan db:seed --class=AdminSeeder --force && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=$PORT
     ```
   - After the first successful deploy (tables created), you can leave this
     start command as-is permanently — `migrate --force` is safe to run on
     every restart since it skips migrations that already ran.

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
