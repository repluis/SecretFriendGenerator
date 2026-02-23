---
description: How to create a new feature following Modular DDD
---

This workflow ensures that any new feature adheres to the project's Modular DDD architecture.

1. **Plan the Domain Logic**
   - Identify the Entity and look for existing ones.
   - Define the `RepositoryInterface` in `Domain/Repositories/`.

2. **Define the Use Case**
   - Create the Use Case class in `Application/UseCases/`.
   - Implement `UseCaseInterface`.
   - // turbo
   - Run `grep_search` to find examples of `UseCaseInterface` implementation.

3. **Implement Infrastructure**
   - Create the Eloquent Repository in `Infrastructure/Persistence/`.
   - Register the binding in `Shared/Infrastructure/Providers/ModuleServiceProvider.php`.

4. **Add Presentation Layer**
   - Create the Controller.
   - Add routes in `routes/api.php` or `routes/web.php`.

5. **Verify**
   - Run tests if available.
   - Test manually via `api.rest`.
