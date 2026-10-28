<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Event;

use Dmytrof\ModelsManagementBundle\Model\{SimpleModelInterface, Target};
use Symfony\Contracts\EventDispatcher\Event;

class LoadTargetModel extends Event
{
    protected ?SimpleModelInterface $model = null;

    public function __construct(
        protected Target $target,
    ) {
    }

    /**
     * Returns target
     */
    public function getTarget(): Target
    {
        return $this->target;
    }

    /**
     * Returns model
     */
    public function getModel(): ?SimpleModelInterface
    {
        return $this->model;
    }

    /**
     * Sets model
     */
    public function setModel(?SimpleModelInterface $model): self
    {
        $this->model = $model;
        return $this;
    }
}