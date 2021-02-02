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

use Symfony\Component\Validator\Constraints as Assert;
use Dmytrof\ModelsManagementBundle\Model\{SimpleModelInterface, TargetedModelInterface, Target};
use Dmytrof\ModelsManagementBundle\Exception\InvalidTargetException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

trait TargetedModelTrait
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     *
     * @Assert\Valid
     */
    protected $target;

    /**
     * @var Target
     */
    protected $targetObj;

    /**
     * Sets event dispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @return TargetedModelInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): TargetedModelInterface
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * Returns event dispatcher
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Sets target
     * @param Target|SimpleModelInterface|null $target
     * @return TargetedModelInterface
     */
    public function setTarget($target): TargetedModelInterface
    {
        $target = ($target instanceof Target) ? $target : new Target($this->eventDispatcher, $target);
        $this->updateTargetData($target);
        $this->targetObj = $target;
        return $this;
    }

    /**
     * Refreshes target
     * @return TargetedModelInterface
     */
    public function refreshTarget(): TargetedModelInterface
    {
        if ($this->getTarget()->getModelId()) {
            $this->getTarget()->refresh();
            $this->updateTargetData($this->getTarget());
        } else {
            $this->target = null;
        }

        return $this;
    }

    /**
     * Updated target data
     * @param Target|null $target
     */
    private function updateTargetData(?Target $target): void
    {
        $this->target = $target->getId() ? $target->toArray() : null;
    }

    /**
     * Returns target
     * @return Target
     */
    public function getTarget(): Target
    {
        if (is_null($this->targetObj)) {
            $this->targetObj = new Target($this->eventDispatcher);
            if (is_array($this->target)) {
                $this->targetObj->fromArray($this->target);
            }
        }
        return $this->targetObj;
    }

    /**
     * Checks target
     * @return bool
     */
    public function hasTarget(): bool
    {
        try {
            return $this->getTarget()->getId() && $this->getTarget()->getModel() instanceof SimpleModelInterface;
        } catch (InvalidTargetException $e) {
            return false;
        }
    }

    public function __destruct()
    {
        if (method_exists('parent', '__destruct')) {
            parent::__destruct();
        }
        $this->_destructTargetedModel();
    }

    /**
     * Destructor actions
     */
    protected function _destructTargetedModel()
    {
        $this->eventDispatcher = null;
        $this->target = null;
        $this->targetObj = null;
    }
}