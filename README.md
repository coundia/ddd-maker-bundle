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

⚠️ **Note:** The entity must exist in `App\Entity\YourEntity` and have constructor and getters / setters.

### 2. Generate a Query

To generate a query and its handler:

```bash
php bin/console make:ddd-query YourEntity QueryName Parameter
```

```

### 3. Generate a Command

To generate a command and its handler:

```bash
php bin/console make:ddd-command YourEntity Action
```

### 4. Inspect the Generated Code

After running the commands, you can check the generated structure:

# EXAMPLE FOR Entity Waller


## Create files (not overwrite)
```
php bin/console make:ddd-full Wallet --force false
``` 
## Create files for Command
```
php bin/console make:ddd-command Wallet UpdatePhone 
``` 
1. created: src/Core/Application/Command/UpdatePhoneWalletCommand.php
2. created: src/Core/Application/CommandHandler/UpdatePhoneWalletCommandHandler.php
3. created: src/Core/Presentation/Controller/UpdatePhoneWalletController.php
4. created: tests/Functional/Wallet/UpdatePhoneWalletCommandControllerTest.php

- Custom it
## Create files for Query
```
php bin/console make:ddd-query Wallet find phoneNumber
``` 
1. created: src/Core/Application/Query/FindByPhoneNumberWalletQuery.php
2. created: src/Core/Application/QueryHandler/FindByPhoneNumberWalletQueryHandler.php
3. created: src/Core/Presentation/Controller/FindByPhoneNumberWalletController.php
4. created: tests/Functional/Wallet/FindByPhoneNumberWalletQueryControllerTest.php

- Custom it

# Api doc

http://127.0.0.1:8000/api/docs

### Run tests

```
php bin/phpunit
```

## remark (Requis)
1. Only field in constructor is checked ,
2. Add all setters and getters

For plus
[docs/usage.md](docs/usage.md)