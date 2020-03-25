<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Model;

use Dmytrof\ModelsManagementBundle\Exception\NotDeletableModelException;

interface ConditionalDeletionInterface
{
    /**
     * Checks if model can be deleted
     * @return bool
     * @throws NotDeletableModelException
     */
    public function canBeDeleted(): bool;
}