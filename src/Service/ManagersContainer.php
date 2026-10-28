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
use Symfony\Component\DependencyInjection\Attribute\{Autoconfigure, AutowireIterator};
use Traversable;

// Public: applications may fetch it from the container directly
#[Autoconfigure(public: true)]
class ManagersContainer implements \IteratorAggregate
{
    protected Collection $managers;

    public function __construct(
        #[AutowireIterator('dmytrof.model.manager')]
        iterable $managers,
    )
    {
        $this->managers = new ArrayCollection();
        foreach ($managers as $manager) {
            $this->addManager($manager);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): Traversable
    {
        return $this->managers->getIterator();
    }

    /**
     * Adds manager
     */
    public function addManager(ManagerInterface $manager): self
    {
        $this->managers->set($manager->getModelClass(), $manager);
        return $this;
    }

    /**
     * Returns manager by model class
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
     */
    public function has(?string $modelClass): bool
    {
        return $modelClass !== null && $this->managers->containsKey($modelClass);
    }

    /**
     * Returns supported models
     */
    public function getModelClasses(): array
    {
        return $this->managers->getKeys();
    }
}