<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\EventSubscriber;

use Dmytrof\ModelsManagementBundle\EventSubscriber\Traits\UpdatedEntitiesTrait;
use Dmytrof\ModelsManagementBundle\Exception\NotDeletableModelException;
use Dmytrof\ModelsManagementBundle\Model\{ConditionalDeletionInterface,
    EventableModelInterface,
    TargetedModelInterface};
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsDoctrineListener(event: Events::postLoad)]
#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
#[AsDoctrineListener(event: Events::postFlush)]
class ModelDoctrineSubscriber
{
    use UpdatedEntitiesTrait;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * Sets registry to target
     */
    protected function setEventDispatcherToEventableModel(LifecycleEventArgs $args): void
    {
        if ($args->getObject() instanceof EventableModelInterface) {
            $args->getObject()->setEventDispatcher($this->eventDispatcher);
        }
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $this->setEventDispatcherToEventableModel($args);
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $this->setEventDispatcherToEventableModel($args);
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof TargetedModelInterface) {
            $this->addUpdatedEntity($entity);
        }
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof TargetedModelInterface) {
            $this->addUpdatedEntity($entity);
        }
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof ConditionalDeletionInterface && !$entity->canBeDeleted()) {
            throw new NotDeletableModelException();
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if ($this->hasUpdatedEntities()) {
            foreach ($this->getUpdatedEntities() as $entity) {
                if ($entity instanceof TargetedModelInterface && !$entity->hasTarget()) {
                    $entity->refreshTarget();
                    $this->setNeedsFlush();
                }
            }
            $this->cleanupUpdatedEntities();
        }
        $this->makeFlushIfNeeded($args->getObjectManager());
    }
}
