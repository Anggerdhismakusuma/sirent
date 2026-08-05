# SI-RENT — Tech Stack & API Documentation

> Platform sewa peralatan hobi multi-vendor (Multi-vendor hobby equipment rental marketplace)

---

## Tech Stack

### Core

| Layer | Technology | Version |
|---|---|---|
| Language | **PHP** | 8.2+ |
| Framework | **Laravel** | 12.x |
| Database | **MySQL** (production) / **SQLite** (dev fallback) | — |
| Frontend Engine | **Blade** (server-rendered views) | — |
| CSS Framework | **Bootstrap** | 5.3.x |
| CSS Preprocessor | **Sass** | 1.77 |
| JavaScript | **Alpine.js** (reactive components) | 3.13.x |
| Build Tool | **Vite** | 7.x |
| Laravel Vite Plugin | `laravel-vite-plugin` | 2.0 |

### JavaScript Libraries (Frontend)

| Package | Version | Purpose |
|---|---|---|
| `alpinejs` | ^3.13.0 | Lightweight reactive UI framework |
| `bootstrap` | ^5.3.3 | UI component library |
| `bootstrap-icons` | ^1.13.1 | Icon set |
| `flatpickr` | ^4.6.13 | Date picker widget |
| `sweetalert2` | ^11.26.25 | Toast & modal alerts |
| `laravel-echo` | ^2.3.7 | WebSocket client (binds to Pusher protocol) |
| `pusher-js` | ^8.5.0 | Pusher WebSocket client |
| `@popperjs/core` | ^2.11.8 | Positioning engine (Bootstrap dependency) |
| `axios` | ^1.11.0 | HTTP client (dev dependency) |

### PHP Packages (Backend)

| Package | Version | Purpose |
|---|---|---|
| `laravel/framework` | ^12.0 | Web application framework |
| `laravel/tinker` | ^2.10.1 | Interactive REPL / debug shell |
| `midtrans/midtrans-php` | ^2.6 | Payment gateway SDK (generate Snap tokens, verify signatures) |
| `pusher/pusher-php-server` | ^7.2 | Server-side Pusher client (broadcast events) |
| `nyholm/psr7` | — | PSR-7 HTTP message implementation |
| `symfony/http-client` | — | HTTP client for outbound requests |
| `symfony/mailer` | — | Email transport layer |
| `railsware/mailtrap-php` | — | Mailtrap SDK (installed, not yet wired into code) |

### Real-Time

| Technology | Role |
|---|---|
| **Laravel Echo** | Client-side WebSocket subscriber (JavaScript) |
| **Pusher protocol** | WebSocket message format |
| **Soketi** | Self-hosted, Pusher-compatible WebSocket server (port 6001, local dev) |

### Queue / Session / Cache

| Concern | Driver |
|---|---|
| Queue | `database` |
| Session | `database` |
| Cache | `file` |

### Mail

| Environment | Service | Host |
|---|---|---|
| Development | **Mailpit** | `127.0.0.1:1025` (SMTP catch-all, no auth) |
| Production | SMTP (configurable via `.env`) | — |
| From Address | `noreply@sirent.id` | — |

### Localization

| Setting | Value |
|---|---|
| Primary Locale | `id` (Bahasa Indonesia) |
| Timezone | `Asia/Jakarta` |
| Locale Middleware | `SetLocale` (per-request) |

### Infrastructure & Deployment

| Platform | Technology |
|---|---|
| Hosting | **Railway** (via Nixpacks build system) |
| Build System | **Nixpacks** (auto-detects PHP 8.4 runtime) |
| Version Control | **Git** — `https://github.com/Anggerdhismakusuma/sirent.git` |
| Dev Server | `php artisan serve` |
| Dev WebSocket | **Soketi** on `ws://127.0.0.1:6001` |
| HTTPS | Forced in non-local environments via `AppServiceProvider` |

---

## External / Third-Party APIs Consumed

### 1. Midtrans Payment Gateway

| Detail | Value |
|---|---|
| **Purpose** | Online payment processing (credit card, bank transfer, e-wallet, etc.) |
| **Mode** | Sandbox (development) / Production (configurable via `MIDTRANS_IS_PRODUCTION`) |
| **SDK** | `midtrans/midtrans-php` ^2.6 (server-side); Snap.js (client-side CDN) |
| **Server Key** | `MIDTRANS_SERVER_KEY` (env) |
| **Client Key** | `MIDTRANS_CLIENT_KEY` (env) |
| **Service File** | `app/Services/MidtransService.php` |
| **Config File** | `config/midtrans.php` |

**Snap (Pop-up Checkout) Flow:**
1. Backend generates a Snap token via `MidtransService::generateSnapToken($rentalRequest)` — order ID format: `SIRENT-XXXXXX-{id}`, fixed 2000 IDR service fee included.
2. Frontend loads Snap.js from CDN:
   - Sandbox: `https://app.sandbox.midtrans.com/snap/snap.js`
   - Production: `https://app.midtrans.com/snap/snap.js`
3. Frontend calls `window.snap.pay(snapToken, callbacks)` — callbacks handle `onSuccess`, `onPending`, `onError`, `onClose`.

**Webhook (server-to-server notification):**
- **Endpoint:** `POST /api/midtrans/webhook` (public, CSRF excluded)
- **Signature:** SHA512 of `order_id + status_code + gross_amount + server_key`
- **Status Mapping:**

| Midtrans Status | App Payment Status |
|---|---|
| `capture`, `settlement` | `paid` |
| `pending` | `pending` |
| `deny`, `cancel`, `expire` | `failed` |
| `expire` | `expired` |
| `refund`, `partial_refund` | `refunded` |

### 2. EMSIFA API Wilayah Indonesia

| Detail | Value |
|---|---|
| **Purpose** | Indonesian province & regency dropdown data (onboarding step 1) |
| **Method** | Client-side `fetch()` (no API key required) |
| **Base URL** | `https://www.emsifa.com/api-wilayah-indonesia/api/` |
| **Endpoints Used** | `provinces.json` (list all provinces), `regencies/{provinceId}.json` (list regencies by province) |
| **Used In** | `resources/views/onboarding/step1.blade.php` |

### 3. Soketi (Self-Hosted WebSocket)

| Detail | Value |
|---|---|
| **Purpose** | Real-time chat messages & notification badges |
| **Protocol** | Pusher-compatible WebSocket |
| **Connection** | `ws://127.0.0.1:6001` (local dev) |
| **Config File** | `soketi.json` |
| **App ID** | `sirent-local` |
| **App Key** | `sirent-app-key` |
| **App Secret** | `sirent-app-secret` |
| **Channels** | `private-conversation.{id}`, `private-user.{id}` |

### 4. Laravel Mail Services

| Service | Usage |
|---|---|
| **Mailpit** (dev) | SMTP catch-all at `127.0.0.1:1025` — receives OTP & verification emails |
| **Mailtrap** | SDK installed (`railsware/mailtrap-php`), available for future wiring |
| **Laravel Mail** | `Mail::send()` for OTP emails; `Password` facade for password reset links |

---

## Public API Endpoints (Exposed by SI-RENT)

### Authentication

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/auth/register` | Guest | Register new user (name, email, password) |
| `POST` | `/auth/verify_otp` | Guest | Verify OTP sent to email after registration |
| `POST` | `/auth/login` | Guest | Login (email, password) |
| `POST` | `/auth/logout` | Auth | Logout current session |
| `POST` | `/auth/forgot-password` | Guest | Send password reset link |
| `POST` | `/auth/reset-password` | Guest | Reset password with token |
| `GET` | `/email/verify/{id}/{hash}` | Signed URL | Verify email address |
| `POST` | `/email/verification-notification` | Auth | Resend verification email |

### Onboarding

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/onboarding/step-1` | Auth | Step 1 — personal info (province/city from EMSIFA API) |
| `POST` | `/onboarding/step-1` | Auth | Save step 1 |
| `POST` | `/onboarding/step-2` | Auth | Step 2 — interests & preferences |
| `POST` | `/onboarding/step-3` | Auth | Step 3 — KTP identity upload & verification |

### Locale

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/locale/{locale}` | — | Switch language (e.g., `id`, `en`) |

### Public Pages

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/` | — | Home page — product listings, search, categories |
| `GET` | `/produk` | — | Product listing / search page |
| `GET` | `/produk/{slug}` | — | Product detail page |
| `GET` | `/about-us` | — | About Us page |
| `GET` | `/toko/{user}` | — | Store page (owner's public profile & products) |
| `GET` | `/toko/{user}/about` | — | Store about section |
| `GET` | `/toko/{user}/reviews` | — | Store reviews |
| `POST` | `/toko/{user}/follow` | Auth | Follow / unfollow a store owner |

### Authenticated User

| Method | Endpoint | Auth | Middleware | Description |
|---|---|---|---|---|
| `GET` | `/api/user/check-email-status` | Auth | — | Check if email is verified |
| `GET` | `/dashboard` | Auth | `account.active` | Borrower dashboard |

### Store Management (Owner)

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/dashboard/store/open` | Auth | Open a store (become an owner) |
| `POST` | `/dashboard/store/products` | Auth | Create a new product listing |
| `PATCH` | `/dashboard/store/products/{id}` | Auth | Update a product listing |
| `DELETE` | `/dashboard/store/products/{id}` | Auth | Delete a product listing |
| `PATCH` | `/dashboard/store/rental-requests/{id}/approve` | Auth | Approve a rental request |
| `PATCH` | `/dashboard/store/rental-requests/{id}/reject` | Auth | Reject a rental request |
| `GET` | `/dashboard/store/transactions` | Auth | List store transactions |
| `POST` | `/dashboard/store/transactions/{id}/dispute` | Auth | File a dispute on a transaction |
| `DELETE` | `/dashboard/store/disputes/{dispute}` | Auth | Withdraw a dispute |

### Chat / Messaging

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/pesan` | Auth | List all conversations |
| `GET` | `/pesan/unread/count` | Auth | Get unread message count (JSON) |
| `POST` | `/pesan/mulai/{product}` | Auth | Start a new conversation about a product |
| `GET` | `/pesan/{conversation}` | Auth | View conversation (page) |
| `POST` | `/pesan/{conversation}` | Auth | Send a message in a conversation |

**WebSocket Channels (real-time):**
- `private-conversation.{id}` — `MessageSent` event (participants only)
- `private-user.{id}` — unread badge count updates (self only)

### Notifications

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/notifications` | Auth | List all notifications |
| `GET` | `/notifications/unread-count` | Auth | Get unread notification count (JSON) |
| `POST` | `/notifications/{id}/mark-read` | Auth | Mark a single notification as read |
| `POST` | `/notifications/mark-all-read` | Auth | Mark all notifications as read |

### Rentals (Borrower)

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/peminjaman` | Auth | Create a rental request |
| `POST` | `/peminjaman/{id}/batal` | Auth | Cancel a rental request |
| `GET` | `/peminjaman/{id}` | Auth | View rental request detail |
| `POST` | `/peminjaman/{id}/rating` | Auth | Submit a rating after rental completion |
| `POST` | `/peminjaman/{rentalRequest}/dispute` | Auth | File a dispute on a rental |

### Checkout & Payment

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/checkout/init` | Auth | Initialize checkout session (30-min expiry, pessimistic locking) |
| `GET` | `/checkout/{token}` | Auth | View checkout page with Snap.js payment |
| `POST` | `/checkout/{token}/pay` | Auth | Trigger Snap token generation & payment |

### Webhook (External)

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/api/midtrans/webhook` | Signature (SHA512) | Midtrans payment status notification (CSRF excluded) |

### Admin

| Method | Endpoint | Auth | Middleware | Description |
|---|---|---|---|---|
| `GET` | `/admin/dashboard` | Auth | `admin` | Admin dashboard overview |
| `PATCH` | `/admin/users/{user}/status` | Auth | `admin` | Suspend/ban/activate a user |
| `GET` | `/admin/disputes` | Auth | `admin` | List all disputes |
| `PATCH` | `/admin/disputes/{dispute}/approve` | Auth | `admin` | Resolve dispute — approve |
| `PATCH` | `/admin/disputes/{dispute}/reject` | Auth | `admin` | Resolve dispute — reject |

### Scheduled Commands

| Command | Schedule | Description |
|---|---|---|
| `payments:expire-pending` | Every hour | Cancel rentals with pending payments older than 24 hours |
| `rentals:auto-reject-expired` | Daily at 23:59 WIB | Auto-reject rental requests past their rental start date |

---

## Broadcast Channels (WebSocket)

| Channel | Authorization | Event |
|---|---|---|
| `private-conversation.{id}` | Authenticated user must be a participant | `MessageSent` |
| `private-user.{id}` | Authenticated user must match `{id}` | Unread badge updates |

---

## Domain Model Summary

| Entity | Key Fields |
|---|---|
| **User** | role (`borrower`, `owner`, `admin`), account_status, verification_status, onboarding fields, rating_avg |
| **Category** | name, slug, icon, parent_id (nested categories) |
| **Product** | owner, category, condition, price_per_day, deposit_amount, status, soft deletes |
| **ProductImage** | product_id, path, is_primary |
| **ProductAvailability** | product_id, blocked_date |
| **RentalRequest** | borrower, product, owner, dates, total_days, quantity, total_price, status, payment fields |
| **Rating** | rater, ratee, rental_request, type (`to_owner`, `to_borrower`), score, review |
| **Conversation** | participants, product (optional) |
| **Message** | conversation, sender, body, attachment, read_at |
| **Dispute** | rental_request, initiator, reason, status (`open`, `in_review`, `resolved`, `rejected`) |
| **Notification** | user, type, data, read_at (database notifications) |
| **Follow** | follower, followed (store owners) |

---

## Dev Tooling

| Tool | Purpose |
|---|---|
| `laravel/pint` ^1.24 | PHP code style fixer |
| `laravel/pail` ^1.2.2 | Log tailing |
| `phpunit/phpunit` ^11.5 | Testing framework |
| `fakerphp/faker` ^1.23 | Seed data generation |
| `nunomaduro/collision` ^8.6 | Improved error pages |
| `concurrently` ^9.0.1 | Run multiple dev processes together (artisan serve + queue + vite + pail) |
| `@soketi/soketi` ^1.6.1 | WebSocket server (local dev) |
| Figma MCP | Design reference via `.mcp.json` (disabled by default) |

### Composer Scripts

| Script | Command |
|---|---|
| `setup` | `composer install && cp .env.example .env && php artisan key:generate && php artisan migrate && npm i && npm run build` |
| `dev` | `concurrently` — runs `artisan serve`, `queue:listen`, `pail`, `npm run dev` |
| `test` | `php artisan test` |

---

## Security Notes

- **Midtrans Webhook:** Signature-verified via SHA512 hash of `order_id + status_code + gross_amount + server_key`. CSRF protection disabled on this route.
- **Checkout:** Session tokens expire after 30 minutes. Pessimistic database locking prevents double booking of the same product dates.
- **Email Verification:** Uses Laravel's built-in signed URL verification (throttled 6 attempts per minute).
- **AppServiceProvider:** Forces HTTPS URL generation in non-local environments (`APP_ENV !== 'local'`).
- **Middleware:** `AdminMiddleware`, `EnsureAccountActive`, `RedirectIfGuest`, `SetLocale`.
- **Secrets:** Midtrans server/client keys, app key, database credentials are managed via `.env` (not in version control). Sandbox keys visible in `.env` are for development only.
