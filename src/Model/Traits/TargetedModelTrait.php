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

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;
use Dmytrof\ModelsManagementBundle\Model\{SimpleModelInterface, TargetedModelInterface, Target};
use Dmytrof\ModelsManagementBundle\Exception\InvalidTargetException;

trait TargetedModelTrait
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

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
     * Sets doctrine
     * @param ManagerRegistry $registry
     * @return TargetedModelInterface
     */
    public function setRegistry(ManagerRegistry $registry): TargetedModelInterface
    {
        $this->registry = $registry;
        return $this;
    }

    /**
     * Returns registry
     * @return null|ManagerRegistry
     */
    public function getRegistry(): ?ManagerRegistry
    {
        return $this->registry;
    }

    /**
     * Sets target
     * @param Target|SimpleModelInterface $target
     * @return TargetedModelInterface
     */
    public function setTarget($target): TargetedModelInterface
    {
        $target = ($target instanceof Target) ? $target : new Target($this->getRegistry(), $target);
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
        $this->getTarget()->refresh();
        $this->updateTargetData($this->getTarget());
        return $this;
    }

    /**
     * @param Target $target
     */
    private function updateTargetData(Target $target): void
    {
        $this->target = $target->toArray();
    }

    /**
     * Returns target
     * @throws InvalidTargetException
     * @return Target
     */
    public function getTarget(): Target
    {
        if (is_null($this->targetObj)) {
            $this->targetObj = new Target($this->getRegistry());
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
            return !is_null($this->target) && $this->getTarget()->getId() && $this->getTarget()->getModel() instanceof SimpleModelInterface;
        } catch (\Exception $e) {
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
        $this->registry = null;
        $this->target = null;
        $this->targetObj = null;
    }
}