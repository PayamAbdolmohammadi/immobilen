Controller refactor workflow:

Goals:
- Keep behavior identical.
- Reduce duplication.
- Move business logic into services where appropriate.
- Keep controllers thin and readable.
- Preserve existing routes and responses.

Refactor process:
1. Analyze current controller responsibilities.
2. Identify duplicated or misplaced business logic.
3. Extract reusable services carefully.
4. Preserve validation and authorization behavior.
5. Add regression tests if logic changes.
6. Avoid large rewrites.
