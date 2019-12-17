<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Data;

use Dmytrof\ModelsManagementBundle\Manager\AbstractDoctrineManager;
use Dmytrof\ModelsManagementBundle\Tests\Data\{SomeModel, SomeModelType};

class SomeModelDoctrineManager extends AbstractDoctrineManager
{
    const MODEL_CLASS = SomeModel::class;
    const FORM_TYPE_CREATE_ITEM = SomeModelType::class;
}