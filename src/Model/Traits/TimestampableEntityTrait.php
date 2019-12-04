<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Model\Traits;

use Gedmo\Timestampable\Traits\TimestampableEntity;

trait TimestampableEntityTrait
{
    use TimestampableEntity, TimestampableClone;
}