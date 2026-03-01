# 5akeMe API (Laravel)

Backend for a prototype that infers user preferences from diagnosis data and connects them to recommendations.

---

## Quick Start

### Requirements

- Docker / Docker Compose
- Node.js (for frontend asset build)

### Start

```bash
# From repository root
docker compose up -d
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate
```

Open **http://localhost:8082** in your browser.

### Local login

Create a test user for email/password login:

```bash
docker compose exec app php artisan user:create-hello
```

Use the printed **email** and **password** at [http://localhost:8082/login](http://localhost:8082/login).

**If you get logged out immediately**  
Set `SESSION_DRIVER=database` in `api/.env`, then run `docker compose exec app php artisan config:clear` and try again.

### Frontend build

```bash
cd api && npm install && npm run build
```

Clear view cache if needed: `docker compose exec app php artisan view:clear` (when using Docker).

---

## Main features

- **Diagnosis**: 2 fixed questions (q1 mood / q2 what you want from drinks) + 1 from each category A/B/C (5 questions total). Scores 10 drink types and outputs primary / candidates / mood.
- **Auth**: Email/password + SNS (Google / LINE / X). SNS can be enabled via `SNS_LOGIN_ENABLED`.
- **Stores, reports, feedback**: Saves 1–5 ratings on diagnosis results for accuracy tuning.
- **Admin**: Filament (users, stores, reports, diagnose feedback).

---

## Tech stack

- **Backend**: PHP 8.x / Laravel
- **Frontend**: Blade + Vite + Tailwind CSS + Alpine.js
- **DB**: MySQL 8
- **Infra**: Docker Compose (web / app / db / python, etc.)

See [../docs/TECH_STACK.md](../docs/TECH_STACK.md) for details.

---

## Diagnosis logic (overview)

- **Config**: `config/diagnose.php` (types, labels, weights, scoring, fixed/category questions)
- **Service**: `app/Services/DiagnoseService.php`
  - `createSession()`: Picks 2 fixed + 1 per category A/B/C to build a session
  - `score()`: Adds points by type from weights; q2 uses `q2_multiplier`. Primary = max score; candidates = within `candidate_width`. Mood from q1.
- **Accuracy**: Run `php artisan diagnose:feedback-stats` for feedback stats. See [../docs/DIAGNOSE_ACCURACY.md](../docs/DIAGNOSE_ACCURACY.md) for tuning.

---

## Documentation (docs/)

| File | Description |
|------|-------------|
| [TECH_STACK.md](../docs/TECH_STACK.md) | Tech stack and container layout |
| [DIAGNOSE_ACCURACY.md](../docs/DIAGNOSE_ACCURACY.md) | Diagnosis accuracy and commands |
| [SNS_LOGIN.md](../docs/SNS_LOGIN.md) | SNS login setup |
| [README.md](../docs/README.md) | DB schema and ER diagram generation (tbls) |
