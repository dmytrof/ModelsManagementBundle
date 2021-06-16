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

use Dmytrof\ModelsManagementBundle\Event\LoadTargetModel;
use Dmytrof\ModelsManagementBundle\Repository\EntityRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ModelEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * ModelEventsSubscriber constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            LoadTargetModel::class => 'loadTargetModel',
        ];
    }

    /**
     * Loads target model
     * @param LoadTargetModel $event
     */
    public function loadTargetModel(LoadTargetModel $event): void
    {
        $target = $event->getTarget();
        if (!$event->getModel()) {
            $className = $target->getClassName();
            $model = null;
            if ($className) {
                $repo = $this->registry->getRepository($className);
                if (!is_null($target->getId())) {
                    $model = $repo->find($target->getId());
//                } else if ($repo instanceof EntityRepositoryInterface) {
//                    $model = $repo->createNew();
//                } else {
//                    $model = new $className();
                }
                $event->setModel($model);
            }
        }
    }
}