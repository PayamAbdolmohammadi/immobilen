# Architecture

The application is a Laravel-based multi-tenant SaaS.

Core principles:
- Tenant data is isolated by mandant_id.
- Business logic should live in services.
- Controllers should coordinate requests only.
- Validation should be handled by Form Requests where useful.
- Authorization should be handled by Policies or Middleware.
- Long-running tasks should run as queued jobs.
- Financial and invoice logic must be deterministic and testable.

Deployment:
- The project uses Docker.
- The application is deployed on AWS.
- Production changes must be small and safe.
- Database migrations must be reviewed before deployment.
