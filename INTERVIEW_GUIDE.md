# Interview Assignment Guide – Senior Developer / Engineering Manager

This document helps you present the project and your decisions in an expert review or interview. Use it to prepare; you can remove this file before submission if you prefer.

---

## 1. Standards & Patterns Used in This Assignment

### What the codebase uses today

| Area | Pattern / Standard | Where |
|------|--------------------|--------|
| **Architecture** | **MVC** (Laravel default) | Controllers handle HTTP, delegate to Eloquent models, return views. |
| **Authorization** | **Policy-based** (Laravel Gates/Policies) | `ShortUrlPolicy`, `InvitationPolicy`, `CompanyPolicy`; `$this->authorize()` in controllers. |
| **Validation** | **Form Request** classes | `StoreShortUrlRequest`, `StoreInvitationRequest`; authorization + rules in one place. |
| **Data access** | **Eloquent directly in controllers** | No Repository pattern; controllers build queries and call `Model::...` / `$query->...`. |
| **Mail** | **Mailable** class | `InvitationMail` + view; Laravel mail abstraction. |
| **Auth** | **Breeze** (session-based) | Login, logout, password reset; no API tokens in this app. |
| **Blade** | **Components + includes** | `<x-app-layout>`, `<x-input-label>`, `@include('short-urls._list')`. |

So: **Laravel’s built-in patterns** (MVC, Policies, Form Requests, Eloquent in controllers). **No Repository pattern, no dedicated Service layer** in the current code.

### How to describe it in the interview

- **“I used Laravel’s default MVC with Policy-based authorization and Form Requests for validation. Controllers talk to Eloquent directly. I didn’t introduce a Repository layer because the assignment scope and query complexity didn’t justify the extra abstraction; I’d add Repositories or a Service layer if we scaled or needed to swap storage or reuse logic in an API.”**

That shows you know the pattern and can justify not using it here.

---

## 2. What an Expert Might Ask / Suggest

### Repository pattern

- **Question:** “Why no Repository pattern?”
- **Answer:** “For this scope, Eloquent in the controller is clear and testable. If we add an API, background jobs, or multiple entry points, I’d extract a `ShortUrlRepository` (or a `ShortUrlService` that uses Eloquent) so all of them share one place for ‘create short URL’ and ‘list by role/filter’. Same for invitations and company-scoped queries.”

### Service layer

- **Question:** “Where’s the business logic?”
- **Answer:** “Right now it’s in controllers and a bit in policies. The main ‘business’ bits are: who can see which short URLs, how invitations and roles work, and how the short code is generated. If this grew (e.g. rate limits, analytics, API), I’d move that into `ShortUrlService` and `InvitationService` so controllers stay thin and logic is reusable and easy to test.”

### Duplication (e.g. filter + scope in multiple controllers)

- **Question:** “There’s repeated query logic for short URLs (index, download, viewAll, dashboard). How would you improve it?”
- **Answer:** “I’d extract a scope or a small service: e.g. `ShortUrl::forUser($user)->filterBy($filter)->...` or a `ShortUrlQueryService` that returns the same query for list/download/view-all. That would live in one place and keep authorization and filtering consistent.”

### Testing

- **Assignment asked for:** Admin/Member can create; SuperAdmin cannot; Admin sees only company URLs; Member sees only own; short URLs redirect.
- **Current state:** You have a `ShortUrlCreationTest` file but it’s still the default example. **Before submission, implement the 5 required tests** (feature tests hitting HTTP and asserting status + DB/redirect). An expert will look for these.

---

## 3. Checklist for Senior / EM Level

### Code & structure

- [ ] **README** – Replace default Laravel README with: PHP/Composer/MySQL versions, clone, `composer install`, `.env`, `php artisan migrate`, `php artisan db:seed`, how to run tests, and SuperAdmin login (e.g. `superadmin@example.com` / `password`).
- [ ] **Tests** – Implement the 5 assignment tests (create as Admin/Member, 403 for SuperAdmin, Admin sees only company URLs, Member only own, public redirect). Run `php artisan test` and ensure they pass.
- [ ] **No dead code** – Remove or finish any placeholder tests (e.g. the current `ShortUrlCreationTest` example).
- [ ] **Consistency** – Same naming and structure everywhere (e.g. route names, controller method names, Blade partials).

### What to be ready to discuss (Senior / EM)

1. **Trade-offs**
   - Why no Repository/Service in this project, and when you would add them.
   - Why Breeze (session) and not Sanctum API for this assignment.
   - Invitation flow: why token in URL, expiry, and how you’d harden it (rate limit, idempotency, audit log).

2. **Security**
   - Roles in DB vs Spatie Permission (simplicity vs flexibility).
   - Policy coverage: every destructive or sensitive action goes through `authorize()`.
   - Mass assignment: only `$fillable` (or `$guarded`) on models.

3. **Scaling / production**
   - Short code generation: uniqueness (DB unique index + retry or `firstOrCreate`).
   - Redirects: 302 vs 301 (tracking vs caching).
   - If traffic grew: cache, queue for invitation emails, read replicas, and where you’d put a Service/Repository layer.

4. **Team & process (EM angle)**
   - How you’d do code review (policies, validation, tests, no business logic in views).
   - How you’d onboard a dev (README, seeding, running tests).
   - How you’d add a new role or new “resource” (e.g. another policy, routes, controller, tests).

---

## 4. One-page “Architecture” summary (for README or interview)

You can add a short “Architecture” or “Design” section to the README (or keep it only in your head for the interview):

- **Stack:** Laravel 11, MySQL, Breeze (session auth).
- **Roles:** SuperAdmin, Admin, Member (DB column on `users`); authorization via Policies.
- **Multi-tenancy:** `companies`; users belong to one company (SuperAdmin has `company_id` null).
- **Main flows:** Invitation (token, email, accept → set password and join company); short URL create/list by role; public redirect `/s/{code}`.
- **Patterns:** MVC, Policy-based auth, Form Requests, Eloquent in controllers; no Repository/Service layer in this scope.
- **Tests:** Feature tests for the 5 required behaviours; run with `php artisan test`.

---

## 5. Quick “If I had more time” list

- Extract short URL query building (filter + role scope) into a scope or a small service.
- Add a `ShortUrlService::create()` used by the controller (and later by an API or job).
- Add rate limiting on invitation and short URL creation.
- Queue invitation emails.
- Add the 5 required tests and keep README focused on setup and how to run them.

Using this, you can describe the current standards (MVC, Policies, Form Requests, no Repository), justify the choices, and show how you’d evolve the code for a senior/EM role.
