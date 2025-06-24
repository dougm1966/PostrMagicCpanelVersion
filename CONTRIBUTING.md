# Contributing to PostrMagic

Thank you for taking the time to contribute!  To keep the codebase healthy and consistent we follow a lightweight, checklist-driven PR process.

---
## Pull Request Checklist
Copy the list below into your PR description and tick each item when complete.

- [ ] **Linted** – All linters/formatters pass (`php-cs-fixer`, `phpstan`, ESLint, Prettier, etc.).
- [ ] **Tested** – All unit & integration tests pass (`vendor/bin/phpunit`, front-end tests, etc.).
- [ ] **Docs Updated** – Relevant docs (`README.md`, guides, ADRs, comments) and **CHANGELOG.md** are updated.
- [ ] **Reviewed** – At least one teammate has been requested for review.

> _Tip_: Use draft PRs early; convert to ready-for-review only when the list is green.

---
## Development Workflow (tl;dr)

1. **Branch** from `main` – `git checkout -b feat/<short-topic>`.
2. Work in small, atomic commits.
3. Run `composer test` / `npm test` & linters locally.
4. Open a **draft** PR early to trigger CI.
5. Once the checklist is ✅, mark the PR **Ready for review**.
6. After approval, **squash & merge** (or follow the repo’s merge strategy).

---
## Code Quality Tools

| Language | Lint / Static Analysis | Formatter |
|----------|-----------------------|-----------|
| PHP      | PHPStan (level 5)     | PHP-CS-Fixer |
| JS/CSS   | ESLint                | Prettier   |

Run `composer analyse` / `composer fix` or `npm run lint` before pushing.

---
## Asking for Help

If you’re blocked, open the PR as a draft with a **“Help wanted”** label or start a discussion in the repo.
