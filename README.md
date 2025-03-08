# DDD/CQRS Maker Bundle

A Symfony bundle that automates the generation of Domain-Driven Design (DDD) code artifacts, including commands, queries, handlers, controllers, API documentation, tests, and more. This bundle provides console commands to quickly scaffold DDD classes for your Symfony projects, following the CQRS (Command Query Responsibility Segregation) pattern.

## Features

- Generate **Commands** and **Handlers** from a given entity.
- Generate **Queries** and **Handlers** from a given entity.
- Generate **Repositories**, **Factories**, **Mappers**, **Value Objects**, and **Aggregates**.
- Support for **Symfony Messenger** to handle DDD commands and queries.
- Use skeleton templates to customize generated code.
- Easily integrate with any Symfony project.

## Installation

### 1. Require the Bundle

If you're developing locally, add a path repository in your project's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../ddd-maker-bundle"
        }
    ],
    "require": {
        "cnd/ddd-maker-bundle": "*"
    }
}
```

Then run:

```bash
composer require cnd/ddd-maker-bundle --dev
```

### 2. Enable the Bundle

If you are using Symfony Flex, the bundle is auto-registered. Otherwise, add the following to your `config/bundles.php`:

```php
return [
    // ...
    Cnd\DddMakerBundle\DddMakerBundle::class => ['all' => true],
];
```

## Usage

Run the following command to see the available DDD generator commands:

```bash
php bin/console list make
```

### 1. Generate a Full CQRS Structure

To generate a complete CQRS structure for an entity, use:

```bash
php bin/console make:ddd-full YourEntity
```

or specify the full namespace:

```bash
php bin/console make:ddd-full "App\\Entity\\YourEntity"
```

Example:

```bash
php bin/console make:ddd-full Transaction
```

⚠️ **Note:** The entity must exist in `App\Entity\YourEntity`.

### 2. Generate a Query

To generate a query and its handler:

```bash
php bin/console make:cqrs-query YourEntity QueryName Parameter
```

Example:

```bash
php bin/console make:cqrs-query Wallet Find phoneNumber
```

### 3. Generate a Command

To generate a command and its handler:

```bash
php bin/console make:cqrs-command YourEntity Action
```

Example:

```bash
php bin/console make:cqrs-command Wallet Update
```

### 4. Inspect the Generated Code

After running the commands, you can check the generated structure:


