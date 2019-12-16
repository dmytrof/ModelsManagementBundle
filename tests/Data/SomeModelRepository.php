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

use Doctrine\ORM\EntityRepository;
use Dmytrof\ModelsManagementBundle\Repository\{EntityRepositoryInterface, Traits\EntityRepositoryTrait};

class SomeModelRepository extends EntityRepository implements EntityRepositoryInterface
{
    use EntityRepositoryTrait;
}