# 164 ГПИЕ — ученическа платформа

Общностен сайт за учениците от **164. ГПИЕ „Мигел де Сервантес“** (София, 8.–12. клас).
Features: whitelist-restricted registration/login, exams & study materials, Q&A (see [Features](#features)).
Room occupancy ("Стаи") is intentionally a placeholder ("Очаквайте скоро").

## Stack

| Layer | Tech |
| --- | --- |
| Backend | Laravel 13 (PHP 8.4), Laravel Breeze (Vue + Inertia stack) |
| Frontend | Vue 3, Inertia.js 2, Tailwind CSS 3, Vite |
| Database | PostgreSQL (18 in Sail, 14+ works natively) |
| Local dev | Laravel Sail (Docker) — primary; native WSL2 — alternative |
| Tests | Pest (feature tests against PostgreSQL), Laravel Pint |
| CI | GitHub Actions (`.github/workflows/ci.yml`) |

UI language is Bulgarian (`APP_LOCALE=bg`), timezone `Europe/Sofia`.

---

## Windows + WSL2 setup

1. Install WSL2 with Ubuntu (PowerShell as admin):
   ```powershell
   wsl --install -d Ubuntu
   ```
2. For the **Sail** path install [Docker Desktop](https://www.docker.com/products/docker-desktop/) and enable
   *Settings → Resources → WSL integration → Ubuntu*.
3. Open the Ubuntu terminal and work **inside the Linux filesystem** (see gotchas below):
   ```bash
   mkdir -p ~/code && cd ~/code
   git clone https://github.com/EmilBuchukov/student-helper-164.git
   cd student-helper-164
   ```

### WSL gotchas

- **Clone into `~/…`, not `/mnt/c/…`.** Projects on the Windows drive are 10–50× slower (file I/O and Vite file watching) and can have permission/line-ending issues.
- Open the project from WSL with `code .` (VS Code + "WSL" extension).
- If `docker` is "command not found" inside Ubuntu, Docker Desktop's WSL integration is not enabled for that distro — enable it and restart the terminal.
- `permission denied while trying to connect to the docker API at unix:///var/run/docker.sock` → your WSL user isn't in the `docker` group: `sudo usermod -aG docker $USER`, then `wsl --shutdown` (PowerShell) and reopen Ubuntu; `docker ps` should work without sudo. With Docker Desktop also make sure it is running and WSL integration is on for your distro.
- `http://localhost` shows the **Apache default page** → Apache is running in WSL and holds port 80 (often pulled in by `apt install php…` without `-cli`). Run `sudo service apache2 stop && sudo systemctl disable apache2` (or `sudo apt remove apache2`), then `sail down && sail up -d`. Alternatively set `APP_PORT=8080` in `.env` and open http://localhost:8080. Native path uses http://localhost:8000.
- Port 5432 already in use? A native PostgreSQL is running in WSL (`sudo service postgresql stop`) or set `FORWARD_DB_PORT=5433` in `.env`.
- Hot reload not picking up changes → you're probably on `/mnt/c`.

---

## Option A — Laravel Sail (Docker, recommended)

Prerequisites: Docker Desktop (with WSL integration) — nothing else; PHP/Composer/Node run inside the container.

```bash
# 1. Install PHP dependencies (one-off container, no local PHP needed)
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
    laravelsail/php84-composer:latest composer install --ignore-platform-reqs

# 2. Environment
cp .env.example .env
#    edit .env: set DB_PASSWORD (any value, e.g. "password") and SEED_ADMIN_PASSWORD

# 3. Start containers (app, pgsql, mailpit)
./vendor/bin/sail up -d

# 4. App key, database, frontend
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```

Open **http://localhost**. Mailpit (password-reset / verification emails): **http://localhost:8025**.

Tip: `alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'` in `~/.bashrc`.

Stop: `sail down` (add `-v` to also delete the database volume).

---

## Option B — Native WSL2 (no Docker)

Prerequisites: PHP 8.4 (+ `pgsql`, `mbstring`, `xml`, `curl`, `zip`, `intl`, `bcmath`), Composer 2, Node 22, PostgreSQL 14+.

```bash
# PHP 8.4 + extensions
sudo add-apt-repository -y ppa:ondrej/php && sudo apt update
sudo apt install -y php8.4-cli php8.4-pgsql php8.4-mbstring php8.4-xml php8.4-curl \
    php8.4-zip php8.4-intl php8.4-bcmath unzip

# Composer
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer

# Node 22 (via nvm)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash && source ~/.bashrc
nvm install 22

# PostgreSQL
sudo apt install -y postgresql
sudo service postgresql start
sudo -u postgres psql -c "CREATE ROLE sail LOGIN PASSWORD 'password';"
sudo -u postgres psql -c "CREATE DATABASE school164 OWNER sail;"
sudo -u postgres psql -c "CREATE DATABASE testing OWNER sail;"
```

Then in the project:

```bash
composer install
npm install
cp .env.example .env
```

Edit `.env`:

```dotenv
DB_HOST=127.0.0.1
DB_PASSWORD=password
MAIL_MAILER=log            # emails (reset links) go to storage/logs/laravel.log
SEED_ADMIN_PASSWORD=...    # choose one
```

```bash
php artisan key:generate
php artisan migrate --seed
composer run dev            # php artisan serve + queue + vite together
```

Open **http://localhost:8000**.

---

## Demo accounts (after `migrate --seed`)

| Role | Email | Password |
| --- | --- | --- |
| Admin | `SEED_ADMIN_EMAIL` from `.env` (default `admin@example.com`) | `SEED_ADMIN_PASSWORD` from `.env` |
| Teacher | `teacher@example.com` | `password` |
| Moderator | `moderator@example.com` | `password` |
| Student | `student1@example.com` … `student25@example.com` | `password` |

Demo passwords are for local development only. If `SEED_ADMIN_EMAIL`/`SEED_ADMIN_PASSWORD` are empty the seeder skips the admin account.

### Trying whitelist registration

Registration only works for an email that is in `allowed_students` and not yet claimed.
The seeder creates unclaimed rows `newstudent1@example.com` … `newstudent8@example.com`.

1. Go to `/register` and register with `newstudent1@example.com` → you get role `student` and the class from the whitelist row; the row is marked claimed.
2. Try `someone@example.com` (not on the list) or `newstudent1@example.com` again → *„Този имейл не е в списъка на училището. Свържи се с администратор.“*
3. As admin, open **Админ → Импорт на ученици** (`/admin/students/import`) and upload [`docs/sample-students.csv`](docs/sample-students.csv) to see the created / skipped / invalid report. CSV format: header `email,grade,letter` (comma or semicolon, UTF-8, letter in Cyrillic, class must exist).

New accounts must verify their email before reaching the dashboard (link in Mailpit or in `storage/logs/laravel.log`).

---

## Features

### Audience scoping (shared)

Exams (and later questions and channels) target an audience via two columns: `scope_type` (`grade` | `class` | `profile`) and `scope_id`.
`App\Models\Concerns\HasAudienceScope` provides:

- `Model::query()->visibleTo($user)` — only items for the user's class, grade or profile;
- `$model->isVisibleTo($user)`, `$model->audienceLabel()` and a `scope()` morphTo relation (eager-load it on lists);
- per-model hooks: `audienceSeesAll()` (default: admins + moderators), `audienceHiddenFrom()`, `audienceIncludesUnscoped()`.

`App\Support\Audience` has `scopesFor($user)`, `userBelongsTo($user, $type, $id)` and `options()` for select boxes
(values are encoded as `"class:5"` and validated with `App\Rules\AudienceScopeRule`). The scope types double as morph aliases
(`Relation::enforceMorphMap` in `AppServiceProvider`).

### Exams & materials (`/exams`)

- List with upcoming exams first (soonest first), then past exams; filters by subject, date range and audience.
- Students/moderators create exams for their own class, grade or profile; admins and teachers for any audience.
  Creator, moderators and admins can edit/delete.
- Teachers see all exams and can download materials, but cannot upload (materials are a student space).
- Materials: pdf, docx, pptx, xlsx, txt, png, jpg (max 10 MB). The server checks the detected MIME type, the extension,
  and that both match. Files are stored under a random name in `materials/{exam_id}/` and only downloaded through
  `GET /materials/{id}/download`, which checks authorization and sends the original filename. Uploads are rate limited (10/min).

### Q&A (`/questions`) — students only

- Questions are school-wide (no scope) or targeted at the author's class/grade/profile (admins: any). Same `visibleTo()` scoping as exams.
- **Teachers are denied completely**: the `no-teachers` route middleware (`DenyTeachers`) returns 403 on every Q&A/report route,
  and `QuestionPolicy`/`AnswerPolicy` + `Question::audienceHiddenFrom()` also deny them (defence in depth).
- Answers, toggleable upvotes (`votes`, one per user/item, not on own content), one accepted answer per question (author toggles it).
- List: search, subject/audience filters, sort by newest / most voted / unresolved.
- **Search uses `ILIKE`** on title + body (escaped `%`/`_`). Chosen over PostgreSQL full-text search because PostgreSQL ships no
  Bulgarian stemmer, users search for partial words, and the data volume of one school is small. If it grows, add a `pg_trgm` GIN index.
- Users edit/delete their own questions/answers; moderators and admins can delete anything (soft deletes, restorable later).
- **Reports** (`reports` table, shared with channel messages): reason required, at most one *open* report per user and item
  (validated + partial unique index `reports_one_open_per_user`).
- Rate limits per user: questions 5/min, answers 10/min, votes 60/min, reports 10/min.

### File storage (local / S3-compatible)

All uploads go through Laravel's `Storage` facade on the disk from `FILESYSTEM_DISK` (or `MATERIALS_DISK` to override it for materials).

- **Local (default):** `FILESYSTEM_DISK=local` → `storage/app/private` (not web-accessible; the local disk's `serve` option is off).
- **S3-compatible (production, ephemeral filesystem):** the `league/flysystem-aws-s3-v3` driver is installed; set

  ```dotenv
  FILESYSTEM_DISK=s3
  AWS_ACCESS_KEY_ID=...
  AWS_SECRET_ACCESS_KEY=...
  AWS_DEFAULT_REGION=auto            # or e.g. eu-central-1
  AWS_BUCKET=school164-materials
  AWS_ENDPOINT=https://<account>.r2.cloudflarestorage.com   # empty for AWS S3
  AWS_USE_PATH_STYLE_ENDPOINT=false  # true for MinIO
  ```

  Keep the bucket **private**; downloads are streamed through the app after the authorization check.
  Switching disks does not migrate existing files.

## Tests & checks

Tests run against the PostgreSQL `testing` database (created automatically by Sail; create it manually natively, see above).

```bash
# Sail
sail artisan test
sail npm run build
sail bin pint --test

# Native
php artisan test
npm run build
vendor/bin/pint --test
```

CI runs Pint, the test suite (PostgreSQL service) and `npm run build` on every push and pull request.

---

## Project structure

```
app/
  Enums/Role.php                         student | moderator | admin | teacher (+ Bulgarian labels)
  Http/Controllers/Admin/                UserController (read-only list), StudentImportController
  Http/Controllers/Auth/                 Breeze auth; RegisteredUserController enforces the whitelist
  Http/Middleware/EnsureUserHasRole.php  `role:admin`, `role:admin,moderator`
  Http/Middleware/EnsureUserIsActive.php logs out deactivated users
  Http/Requests/                         Form Requests (RegisterRequest, LoginRequest, ImportStudentsRequest…)
  Models/                                User, Grade, Profile, SchoolClass (table `classes`), AllowedStudent
  Policies/ChannelPolicy.php             placeholder — teachers are always denied
  Services/StudentImportService.php      CSV parsing/validation/import report
config/school.php                        seed admin settings
database/migrations|factories|seeders/
docs/sample-students.csv
lang/bg/                                 auth, validation, passwords, pagination, ui (frontend strings)
resources/js/
  Layouts/AppLayout.vue                  responsive navbar + mobile menu + footer
  Pages/                                 Welcome, Dashboard, ComingSoon, Auth/*, Profile/*, Admin/*
  i18n.js                                `$t('key')` helper fed from lang/bg/ui.php via Inertia
routes/web.php, routes/auth.php
tests/Feature/                           auth, registration, profile, dashboard, admin access, CSV import
```

Frontend strings live in `lang/bg/ui.php`; they are shared to Vue as flattened keys (`nav.home`) and used with `$t('nav.home')`.

---

## Database schema

| Table | Columns | Notes |
| --- | --- | --- |
| `grades` | `id`, `number` | unique, CHECK 8–12 |
| `profiles` | `id`, `name` | e.g. Испански език, Информатика |
| `classes` | `id`, `grade_id` → grades, `letter`, `profile_id` → profiles (nullable) | unique (`grade_id`, `letter`); grade delete restricted, profile delete nulls |
| `users` | Breeze columns + `role` (enum), `class_id` → classes (nullable), `is_active` | class delete nulls `class_id` |
| `allowed_students` | `id`, `email` (unique), `class_id` → classes, `claimed_at`, `claimed_by` → users | class delete restricted; user delete nulls `claimed_by` |

```mermaid
erDiagram
    GRADES ||--o{ CLASSES : has
    PROFILES |o--o{ CLASSES : specializes
    CLASSES |o--o{ USERS : "contains"
    CLASSES ||--o{ ALLOWED_STUDENTS : "whitelists for"
    USERS |o--o| ALLOWED_STUDENTS : claims

    GRADES {
        bigint id PK
        smallint number "8-12, unique"
    }
    PROFILES {
        bigint id PK
        string name
    }
    CLASSES {
        bigint id PK
        bigint grade_id FK
        string letter
        bigint profile_id FK "nullable"
    }
    USERS {
        bigint id PK
        string name
        string email "unique"
        string password
        enum role "student|moderator|admin|teacher"
        bigint class_id FK "nullable"
        boolean is_active
        timestamp email_verified_at
    }
    ALLOWED_STUDENTS {
        bigint id PK
        string email "unique"
        bigint class_id FK
        timestamp claimed_at "nullable"
        bigint claimed_by FK "nullable"
    }
```

## Out of scope (next phases)

Exams & materials, Q&A, room occupancy/bookings, clubs & events, student-only channels/chat, real-time features.
