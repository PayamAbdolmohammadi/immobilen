Feature development workflow:

1. Analyze the existing architecture first.
2. Identify all affected files before editing.
3. Create an implementation plan.
4. Respect tenant isolation and mandant_id rules.
5. Keep changes small and production-safe.
6. Reuse existing services, actions and patterns where possible.
7. Avoid duplicate business logic.
8. Add or update tests for important business logic.
9. Review authorization and validation.
10. Suggest a clean commit message after implementation.

Important:
- Do not rewrite unrelated files.
- Do not change Docker, AWS or CI configuration unless explicitly requested.
- Preserve existing routes and APIs.
