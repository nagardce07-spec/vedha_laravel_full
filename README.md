# Vedha Admin Panel — Laravel Backend

This is the full backend for the Vedha Admin Panel (Dashboard, Categories, Author,
Customers, Book, Book Review, Book Likes, Trending Books, Notifications,
Onboarding Screens, Book Suggestions, Admob, App Settings, Quick Share,
Settings, Privacy Policy, Terms of Uses) plus the public API the Flutter app consumes.

## 1. Create a fresh Laravel project (if you don't have one yet)

```
composer create-project laravel/laravel vedha-backend
cd vedha-backend
```

## 2. Copy these files into your project

- `database/migrations/*` → `database/migrations/`
- `app/Models/*` → `app/Models/`
- `app/Http/Controllers/Admin/*` → `app/Http/Controllers/Admin/`
- `app/Http/Controllers/Auth/*` → `app/Http/Controllers/Auth/`
- `app/Http/Controllers/Api/*` → `app/Http/Controllers/Api/`
- `resources/views/*` → `resources/views/`
- `routes/web.php` → replace/merge into your `routes/web.php`
- `routes/api.php` → replace/merge into your `routes/api.php`
- `database/seeders/AdminSeeder.php` → `database/seeders/`
- Merge `config/auth.php` guards/providers into your existing `config/auth.php`

## 3. Install extra packages

```
composer require james-heinrich/getid3 laravel/sanctum
```

`getid3` auto-detects audio file duration on upload (used in `BookController`).
`sanctum` protects the review/like API endpoints for logged-in app users.

## 4. Configure `.env`

```
DB_CONNECTION=mysql
DB_DATABASE=vedha
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

## 5. Run migrations, link storage, seed the first admin

```
php artisan migrate
php artisan storage:link
php artisan db:seed --class=AdminSeeder
```

Default login after seeding:
- Email: `admin@vedha.com`
- Password: `password`
(Change this from the Settings → Password screen after first login.)

## 6. Serve it

```
php artisan serve
```

Visit `http://localhost:8000/login` — this is the "Welcome Back / Login to your
Dashboard" screen. After login you land on `/admin/dashboard`.

## Notes on structure

- **Single-row settings tables** (`admob_settings`, `app_settings`,
  `quick_share_settings`, `general_settings`) always operate on row `id = 1`
  via each model's `::current()` helper — no need to seed them manually,
  they're created on first access with sensible defaults.
- **Pages** (`privacy-policy`, `terms-of-uses`) are stored in a single `pages`
  table keyed by `slug`, holding the rich-text HTML from the Quill editor.
- **Books** support both `type = full` (single audio file/URL) and
  `type = chapter` (managed via the nested `book_chapters` table, reachable
  from the ➕ icon in the Book list).
- **Trending Books** and **Onboarding Screens** support drag-and-drop
  re-ordering (native HTML5 drag API → `PATCH .../reorder`).
- All file uploads (covers, author photos, audio, onboarding images, branding)
  go through Laravel's `Storage::disk('public')` — swap the `storage_provider`
  setting to S3/Spaces later by changing `config/filesystems.php` disks.
- The **Flutter app** should call the endpoints in `routes/api.php`
  (`/api/books`, `/api/categories`, `/api/authors`, `/api/trending-books`,
  `/api/book-suggestions`, etc.) — these return the exact same data
  the admin panel manages, via `cover_url`/`resource_full_url` accessors
  that already point at the public storage URL.
