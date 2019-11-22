<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Service;

use Dmytrof\ModelsManagementBundle\{Exception\ManagerException, Manager\ManagerInterface};
use Doctrine\Common\Collections\{Collection, ArrayCollection};

class ManagersContainer implements \IteratorAggregate
{
    /**
     * @var Collection|ManagerInterface[]
     */
    protected $managers;

    /**
     * ManagersContainer constructor.
     * @param iterable $managers
     */
    public function __construct(iterable $managers)
    {
        $this->managers = new ArrayCollection();
        foreach ($managers as $manager) {
            $this->addManager($manager);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return $this->managers->getIterator();
    }

    /**
     * Adds manager
     * @param ManagerInterface $manager
     * @return ManagersContainer
     */
    public function addManager(ManagerInterface $manager): self
    {
        $this->managers->set($manager->getModelClass(), $manager);
        return $this;
    }

    /**
     * Returns manager by model class
     * @param null|string $modelClass
     * @return ManagerInterface
     */
    public function getManagerForModelClass(?string $modelClass): ManagerInterface
    {
        if (!$this->has($modelClass)) {
            throw new ManagerException(sprintf('Unsupported model class %s', $modelClass));
        }
        return $this->managers->get($modelClass);
    }

    /**
     * Checks if container has manager for model class
     * @param null|string $modelClass
     * @return bool
     */
    public function has(?string $modelClass): bool
    {
        return $this->managers->containsKey($modelClass);
    }

    /**
     * Returns supported models
     * @return array
     */
    public function getModelClasses(): array
    {
        return $this->managers->getKeys();
    }
}