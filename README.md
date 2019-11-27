DmytrofModelsManagementBundle
====================

This bundle helps you to manage your models and entities (from DB, 3rd-party API, etc.) for your Symfony 4/5 application

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
        
        