# Coding Standards

PHP / Laravel:
- Follow PSR-12.
- Use clear class and method names.
- Keep controllers thin.
- Use services for business workflows.
- Use policies for authorization.
- Use migrations for database changes.
- Avoid duplicated business logic.

Database:
- Always consider mandant_id for tenant-owned tables.
- Add indexes for frequently filtered columns.
- Be careful with destructive migrations.

Testing:
- Important business logic needs tests.
- Tenant isolation must be tested.
- Bugs should get regression tests.

AI usage:
- Cursor must analyze first.
- Cursor should create a plan before editing.
- Cursor should not rewrite unrelated files.
