# ddd/Cqrs Maker Bundle

A Symfony bundle that generates ddd code artifacts (commands, queries, handlers, Controllers, api docs, Tests etc.) from a entities. 
This bundle provides console commands to quickly scaffold ddd classes for your Symfony projects.

## Features

- Generate Command and handlers from a given entity.
- Generate Query and handlers from a given entity.
- etc..
- php bin/console list make (to see command prefix **ddd-**)
- Use skeleton templates to customize generated code.
- Leverage Symfony Messenger for handling ddd commands and queries.
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

Then run 

`composer require cnd/ddd-maker-bundle --dev`.


If you are using Symfony Flex, the bundle is auto-registered. Otherwise, add the following to your config/bundles.php:

```php
return [
// ...
Cnd\DddMakerBundle\DddMakerBundle::class => ['all' => true],
];

```
**Usage**

Run the following command to generate a command class and handler for a given entity:

```bash
php bin/console make:cqrs-full YourEntity
Or
php bin/console make:cqrs-full "App\\Entity\\YourEntity"
OR 

e.g  php bin/console make:cqrs-full Transaction

```
nb: Transaction must exist  in "App\\Entity\\Transaction"

Structure of generated code

` tree -L 5 src`
```
bin/console make:cqrs-full wallet
bin/console make:ddd-full wallet

symfony-ddd-todo git:(main) ❯❯❯ tree -L 5 src
src
├── Core
│   ├── Application
│   │   ├── Command
│   │   │   └── CreateWalletCommand.php
│   │   ├── CommandHandler
│   │   │   └── CreateWalletCommandHandler.php
│   │   ├── DTO
│   │   │   ├── WalletDTO.php
│   │   │   ├── WalletRequestDTO.php
│   │   │   └── WalletResponseDTO.php
│   │   ├── Mapper
│   │   │   └── Wallet
│   │   │       ├── WalletMapper.php
│   │   │       └── WalletMapperInterface.php
│   │   ├── Query
│   │   │   ├── FindByIdWalletQuery.php
│   │   │   └── FindWalletPaginatedQuery.php
│   │   ├── QueryHandler
│   │   │   ├── FindByIdWalletQueryHandler.php
│   │   │   └── FindWalletPaginatedQueryHandler.php
│   │   └── Service
│   │       ├── WalletCreate.php
│   │       ├── WalletDelete.php
│   │       ├── WalletFind.php
│   │       └── WalletUpdate.php
│   ├── Domain
│   │   ├── Aggregate
│   │   │   └── WalletModel.php
│   │   ├── Event
│   │   │   ├── WalletEventCreated.php
│   │   │   ├── WalletEventDeleted.php
│   │   │   └── WalletEventUpdated.php
│   │   ├── Exception
│   │   │   └── WalletException.php
│   │   ├── Repository
│   │   │   └── WalletRepositoryInterface.php
│   │   ├── UseCase
│   │   │   ├── WalletCreateInterface.php
│   │   │   ├── WalletDeleteInterface.php
│   │   │   ├── WalletFindInterface.php
│   │   │   └── WalletUpdateInterface.php
│   │   └── ValueObject
│   │       ├── WalletBalance.php
│   │       ├── WalletId.php
│   │       ├── WalletPhoneNumber.php
│   │       └── WalletProvider.php
│   ├── Infrastructure
│   │   ├── DataFixtures
│   │   │   └── WalletFixtures.php
│   │   ├── Factory
│   │   │   └── WalletFactory.php
│   │   ├── Persistence
│   │   │   └── WalletRepository.php
│   │   ├── Story
│   │   │   └── WalletStory.php
│   │   └── Voters
│   │       └── WalletVoter.php
│   └── Presentation
│       └── Controller
│           ├── CreateWalletController.php
│           ├── FindByIdWalletController.php
│           ├── FindWalletController.php
│           ├── WalletBulkCreateController.php
│           ├── WalletCreateController.php
│           ├── WalletDeleteController.php
│           ├── WalletListController.php
│           └── WalletUpdateController.php
├── Entity
│   ├── Budget.php
│   ├── Category.php
│   ├── Transaction.php
│   └── Wallet.php

```

Example of Query 
```
bin/console make:cqrs-query wallet find phoneNumber
```

Example of Command
```
bin/console make:cqrs-command wallet update
```

