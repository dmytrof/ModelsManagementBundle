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

trait TimestampableClone
{
    public function __clone()
    {
        if (is_callable(['parent', '__clone'])) {
            parent::__clone();
        }
        $this->_cloneTimestampable();
    }

    /**
     * Cloning actions
     */
    protected function _cloneTimestampable(): void
    {
        $this->createdAt = null;
        $this->updatedAt = null;
    }
}