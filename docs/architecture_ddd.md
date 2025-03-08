# Architecture

src/
├── Core/
│   ├── Application/
│   │   ├── Command/
│   │   ├── CommandHandler/
│   │   ├── DTO/
│   │   ├── Mapper/
│   │   ├── Query/
│   │   ├── QueryHandler/
│   │   └── Service/
│   ├── Domain/
│   │   ├── Aggregate/
│   │   ├── Event/
│   │   ├── Exception/
│   │   ├── Repository/
│   │   ├── UseCase/
│   │   └── ValueObject/
│   ├── Infrastructure/
│   │   ├── DataFixtures/
│   │   ├── Factory/
│   │   ├── Persistence/
│   │   ├── Story/
│   │   └── Voters/
│   └── Presentation/
│       └── Controller/
etc...

# Core

The **Core** directory encapsulates the application's fundamental business logic, organized into several key subdirectories:

## Application

Serves as an intermediary between user interactions and business processes.

- **Command**: Contains classes that represent actions to be executed.
- **CommandHandler**: Holds the logic to process commands.
- **DTO (Data Transfer Objects)**: Structures for transferring data between layers.
- **Mapper**: Classes responsible for converting between domain models and DTOs.
- **Query**: Objects that define data retrieval operations.
- **QueryHandler**: Logic to handle data retrieval operations.
- **Service**: Contains business logic services implementing application use cases.

## Domain

Represents the core business concepts and rules.

- **Aggregate**: Clusters related entities and ensures business rules are consistently applied.
- **Event**: Captures significant occurrences within the domain.
- **Exception**: Defines errors specific to business logic.
- **Repository**: Interfaces for data access operations.
- **UseCase**: Interfaces outlining specific business operations.
- **ValueObject**: Objects defined by their attributes, without distinct identity.

## Infrastructure

Provides technical implementations supporting the domain.

- **DataFixtures**: Initial data sets for database seeding.
- **Factory**: Classes responsible for creating complex objects.
- **Persistence**: Concrete implementations of repository interfaces, handling data storage.
- **Story**: Classes representing business processes or workflows.
- **Voters**: Implement access control logic based on business rules.

## Presentation

Manages user interactions and input/output operations.

- **Controller**: Handles HTTP requests and responses, acting as an interface between the user and the application.
