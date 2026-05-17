## Architectural Checklist

Before requesting a review, please ensure your PR adheres to our enterprise standards. Failure to check these boxes will result in an automated block from our CI pipeline.

### Backend Guardrails
- [ ] **No `SELECT *`**: I have used explicit column selections (e.g., `select('id', 'name')`) for all database queries.
- [ ] **N+1 Prevention**: I have utilized `with()` eager loading for any relationships accessed in loops.
- [ ] **Thin Controllers**: I have extracted all heavy business logic (calculations, third-party APIs, transactions) into a dedicated `Service` class.
- [ ] **Guard Clauses**: I have flattened my logic. I am using early returns instead of deeply nested `if/else` ladders.

### Frontend Guardrails
- [ ] **Atomic Components**: I have broken down massive UI chunks into reusable Blade components (files are under 200 lines).
- [ ] **Isolated State**: I have kept my Alpine.js `x-data` states scoped directly to the leaf elements that need them, avoiding full-page re-renders.

### The Boy Scout Rule
- [ ] **Micro-Refactoring**: I left the surrounding codebase cleaner than I found it (e.g., refactored an adjacent bloated function or replaced an old inline array with a DTO).

---
*Note: Our automated pipeline will run Laravel Pint before merging to enforce cyclomatic complexity and syntax constraints.*
