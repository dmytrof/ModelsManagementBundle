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

use Dmytrof\ModelsManagementBundle\Model\TargetedModelInterface;
use Doctrine\Common\{EventSubscriber, Persistence\Event\LifecycleEventArgs};

class NewTargetDoctrineListener implements EventSubscriber
{
    /**
     * @var TargetedModelInterface
     */
    protected $entity;

    /**
     * NewTargetDoctrineListener constructor.
     * @param TargetedModelInterface $entity
     */
    public function __construct(TargetedModelInterface $entity)
    {
        $this->entity = $entity;
    }

    /**
     * Get subscribed events
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist',
        ];
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $className = $this->entity->getTarget()->getClassName();
        if ($args->getObject() instanceof $className && $args->getObject()->getId() && $args->getObject() === $this->entity->getTarget()->getModel()) {
            $this->entity->refreshTarget();
            $args->getObjectManager()->flush();
        }
    }
}