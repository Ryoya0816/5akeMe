# 5akeMe
A prototype that infers user preferences from short quiz data and turns them into recommendations.

## Overview
5akeMe is a web app that estimates a user’s taste profile from a few quiz questions and displays the result as:
- **primary**: the best-matching type
- **candidates**: alternative close matches
- **mood**: a derived mood label from answers

In the next step, the result will be connected to “recommended drinks / stores” to complete a full recommendation flow.

## Business Use Cases (for companies)
- Turn quiz/survey answers into **product/store/content recommendations**
- Improve campaign landing pages (Quiz → Result → Navigation)
- Start simple, then expand to DB-driven recommendations, A/B testing, and iterative tuning

## Demo / Screenshots
- Top: “noren” (Japanese curtain) animation + Welcome button (WIP)
- Result: primary + candidates + mood

> Adding GIFs/screenshots here makes the project instantly understandable on GitHub.

## Key Features
- Create a diagnosis session (2 fixed questions + 1 question from each category A/B/C)
- Scoring to 10 drink types (with a multiplier applied only to q2)
- Output: primary / candidates / mood
- UI: Top-page visual effects (WIP)
- Result → Store guidance (dummy flow → planned)

## Tech Stack
- Backend: Laravel
- Frontend: Blade (main) + JavaScript (UI effects/support)
- Styling: Tailwind CSS + `resources/css/app.css` (shared design tokens)
- Infrastructure: Docker Compose
- Database: MySQL (assumed)

## Architecture (Diagnosis Logic)
- `app/Services/DiagnoseService.php`
  - `createSession`: generates 2 fixed questions (q1, q2) + 1 question from each of A/B/C
  - `score`: reads `config/diagnose.php` (types/labels/weights/scoring) to add scores to 10 drink types
  - applies `q2_multiplier` only to q2
  - determines `mood` (lively/chill/silent/light/strong) from q1 answer (a–e)
  - selects candidates within `candidate_width` and sorts them by score (desc)
  - returns: primary / candidates / mood

## Local Setup
### Requirements
- Docker / Docker Compose
- Node.js (if using Vite for frontend assets)

### Start (Backend)
```bash
docker compose up -d
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan migrate
