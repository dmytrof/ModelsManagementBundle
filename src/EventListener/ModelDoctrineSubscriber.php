<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\EventListener;

use Dmytrof\ModelsManagementBundle\Model\{TargetedModelInterface, Target};
use Doctrine\Common\{EventSubscriber, Persistence\Event\LifecycleEventArgs, Persistence\ManagerRegistry};

class ModelDoctrineSubscriber implements EventSubscriber
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * ModelDoctrineSubscriber constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Get subscribed events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postLoad',
            'prePersist',
            'postPersist',
            'preUpdate',
            'postUpdate',
        ];
    }

    /**
     * Sets registry to target
     * @param LifecycleEventArgs $args
     */
    protected function setRegistryToTargetedModel(LifecycleEventArgs $args): void
    {
        if ($args->getObject() instanceof TargetedModelInterface) {
            $args->getObject()->setRegistry($this->registry);
        }
    }

    /**
     * Updates target
     * @param LifecycleEventArgs $args
     */
    protected function updateTarget(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof TargetedModelInterface && $entity->getTarget()->getClassName()) {
            if (!$entity->getTarget()->getId() && $entity->getTarget()->getModel()->getId()) {
                $entity->refreshTarget();
                $args->getObjectManager()->flush();
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $this->setRegistryToTargetedModel($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setRegistryToTargetedModel($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->setRegistryToTargetedModel($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateTarget($args);
    }

    /**
     * @param LifecycleEventArgs $args
     * @return void
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateTarget($args);
    }
}