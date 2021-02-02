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
    /**
     * @var Target
     */
    protected $target;

    /**
     * @var SimpleModelInterface
     */
    protected $model;

    /**
     * LoadTargetModel constructor.
     * @param Target $target
     */
    public function __construct(Target $target)
    {
        $this->target = $target;
    }

    /**
     * Returns target
     * @return Target
     */
    public function getTarget(): Target
    {
        return $this->target;
    }

    /**
     * Returns model
     * @return SimpleModelInterface|null
     */
    public function getModel(): ?SimpleModelInterface
    {
        return $this->model;
    }

    /**
     * Sets model
     * @param SimpleModelInterface|null $model
     * @return $this
     */
    public function setModel(?SimpleModelInterface $model): self
    {
        $this->model = $model;
        return $this;
    }
}