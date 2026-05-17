# Contributing Guidelines

Welcome to the team! To prevent our codebase from slipping back into monolithic bloat, we adhere strictly to **The Boy Scout Rule**.

## The Boy Scout Rule
*“Always leave the campground cleaner than you found it.”*

### What this means for you:
Whenever you open a file to build a new feature or fix a bug, you are expected to perform a small, continuous micro-refactoring on the surrounding code. 

1. **Extract inline logic**: If you see a heavy calculation, extract it into a pure utility function in `app/Helpers/`.
2. **Flatten nested logic**: If you see a V-shaped `if/else` ladder, rewrite it using guard clauses (early returns).
3. **Type your payloads**: If you see a raw array being passed into a Service class, convert it into a strictly typed DTO (Data Transfer Object).
4. **Fix a query**: If you see a `SELECT *` or an N+1 vulnerability, replace it with specific column selections and eager loading (`with()`).

### PR Requirements
Your pull request will not be approved unless you can demonstrate at least one micro-refactoring improvement adjacent to your feature code. This ensures our technical debt decreases every single day.
