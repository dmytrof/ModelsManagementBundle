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

use Dmytrof\ModelsManagementBundle\Model\{TargetedModelInterface, Traits\TargetedModelTrait};

class TargetedModel implements TargetedModelInterface
{
    use TargetedModelTrait;
}