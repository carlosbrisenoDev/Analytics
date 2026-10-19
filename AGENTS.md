# Unlock Brands Analytics Dashboard - Agent Guidelines & Context

## 1. Project Overview
**Unlock Brands Analytics Dashboard** is a high-performance, lightweight web analytics platform built on Laravel. It is engineered to capture high-throughput event telemetry (page views, clicks, dwell times, and mouse/touch heatmap traces) without requiring API tokens or authentication on client websites.

## 2. Technical Stack
- **Framework:** Laravel 13.x (PHP ^8.3 / ^8.4)
- **Runtime Note (macOS / Local):**
  - Default terminal `php` may resolve to an older version (e.g. 7.4). The project dependencies require PHP >= 8.4.1.
  - Homebrew PHP 8.5 is located at `/opt/homebrew/Cellar/php/8.5.11/bin/php` (or export `/opt/homebrew/opt/php/bin` to `PATH`).
- **Database:** MySQL (`DB_CONNECTION=mysql` in `.env`). Base schema in `db_structure.sql`, patches in `db_updates.sql`.
- **Frontend / Assets:** Blade templates + Tailwind CSS v4 + Vite (`package.json`).
- **Billing:** Stripe integration (`stripe/stripe-php`) with configurable keys via database (`SystemConfig`) or `.env`.

## 3. Key Directory Structure
```
analytics-dashboard/
├── app/
│   ├── Http/Controllers/
│   │   ├── AuthController.php          # Authentication (login/logout)
│   │   ├── DashboardController.php     # Main analytics computation & admin actions
│   │   ├── IngestionController.php     # Public event capture (/api/track, /api/activity)
│   │   └── SubscriptionController.php  # Stripe billing & webhooks
│   └── Models/
│       ├── AllowedDomain.php           # CORS allowed origins
│       ├── Event.php                   # Analytics events storage
│       ├── SystemConfig.php            # Dynamic settings (Stripe keys, etc.)
│       └── User.php                    # System users (master, editor, viewer)
├── config/                             # Laravel configurations (cors.php, database.php, etc.)
├── docs/                               # Detailed technical docs (Obsidian compatible)
├── public/                             # Public web root & static assets
├── resources/
│   └── views/
│       ├── dashboard.blade.php         # Analytics dashboard UI
│       └── login.blade.php             # Login view
├── routes/
│   ├── api.php                         # Public ingestion and webhook endpoints
│   └── web.php                         # Dashboard and authenticated admin routes
└── GUIA_CONEXION_WEBSITE.md            # Client integration snippet & guide
```

## 4. Architecture & Data Flow
1. **Client Telemetry (`GUIA_CONEXION_WEBSITE.md`):**
   - Client websites embed an asynchronous tracker that posts beacons to `/api/track` or `/api/activity`.
   - Event types include: `view`, `click`, `cta_click`, `duration`, heatmap coords (`x_coord`, `y_coord`).
2. **Ingestion Layer (`IngestionController`):**
   - Operates without Bearer token auth.
   - Rate limiting is enforced per session (`RateLimiter`).
   - Normalizes input durations (converts millisecond inputs, caps visits to 30 min / 1800s to avoid skewed averages).
3. **Analytics Processing (`DashboardController`):**
   - Filters events by selected site and date range.
   - Calculates retention time per session/visit (clustering consecutive events within 30-min windows).
   - Generates conversion funnels, health score, heatmap distributions, and lead metrics.
4. **Access Control & Roles (`docs/authentication.md`):**
   - `master`: Full system control (user management, allowed domains, Stripe configuration).
   - `editor`: Can view dashboard and clear events for sites.
   - `viewer`: Read-only access to analytics dashboard.

## 5. Development & Common Commands
- **Artisan (PHP 8.5):**
  ```bash
  /opt/homebrew/Cellar/php/8.5.11/bin/php artisan serve
  /opt/homebrew/Cellar/php/8.5.11/bin/php artisan tinker
  /opt/homebrew/Cellar/php/8.5.11/bin/php artisan route:list
  ```
- **Frontend / Assets:**
  ```bash
  npm run dev      # Start Vite dev server
  npm run build    # Compile assets for production
  ```
- **Database Setup:**
  - Import `db_structure.sql` for initial tables (`events`, `users`, `allowed_domains`, `system_configs`).
  - Run `db_updates.sql` if incremental schema updates are present.

## 6. Code Conventions & Best Practices
- **Performance First in Ingestion:** Keep `IngestionController::track` as lean as possible. Avoid synchronous heavy lookups or external API calls.
- **Data Normalization:** Always filter/sanitize incoming client data (durations, coordinates, user agents, referrers) before persisting to `events`.
- **CORS Management:** Respect origins verified in `AllowedDomain`.
- **Security:** Guard master/editor endpoints with proper role checks (`Auth::user()->role`).
