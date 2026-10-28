DmytrofModelsManagementBundle
====================

This bundle helps you to manage your models and entities (from DB, 3rd-party API, etc.) for your Symfony application

## Requirements

* PHP 8.3 or higher
* Symfony 6.4 or higher
* Doctrine ORM 3 with DoctrineBundle 2.11+
* gedmo/doctrine-extensions 3.16+ (Timestampable and Blameable listeners are registered by the bundle)

## Installation

### Step 1: Install the bundle

    $ composer require dmytrof/models-management-bundle

### Step 2: Enable the bundle

    <?php
        // config/bundles.php

        return [
            // ...
            Dmytrof\ModelsManagementBundle\DmytrofModelsManagementBundle::class => ['all' => true],
        ];

## Upgrading to Symfony 6.4 / Doctrine ORM 3

* Mapping (Doctrine, Gedmo) and validation use PHP attributes; `doctrine/annotations` is no longer needed.
* Doctrine listeners are registered with `doctrine.event_listener` tags. `ModelDoctrineSubscriber` and
  `ModificationEventsDoctrineSubscriber` no longer implement `Doctrine\Common\EventSubscriber`.
* `AbstractDoctrineManager::getIterator()` uses `Query::toIterable()` and yields entities directly
  (ORM 2 `Query::iterate()` yielded `[0 => $entity]` rows).
