<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Dmytrof\ModelsManagementBundle\{Event\LoadTargetModel, Exception\InvalidTargetException, Exception\TargetException};
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class Target
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var string
     *
     * @Assert\NotNull()
     */
    protected $className;

    /**
     * @var int
     *
     * @Assert\NotNull()
     */
    protected $id;

    /**
     * @var SimpleModelInterface
     */
    protected $model;

    /**
     * Target constructor.
     * @param EventDispatcherInterface $eventDispatcher
     * @param SimpleModelInterface|null $model
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, ?SimpleModelInterface $model = null)
    {
        $this->setEventDispatcher($eventDispatcher);
        $this->model = $model;
        $this->refresh();
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
     * Sets event dispatcher
     * @param EventDispatcherInterface $eventDispatcher
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): self
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * Returns class name of target
     * @return string
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * Sets class name
     * @param null|string $className
     * @return Target
     */
    protected function setClassName(?string $className): self
    {
        if (!is_null($this->getClassName()) && $this->getClassName() !== $className) {
            throw new TargetException(sprintf('Class name of the target is already defined'));
        }
        $this->className = $className;

        return $this;
    }

    /**
     * Returns id of target
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets id
     * @param mixed $id
     * @return Target
     */
    protected function setId($id): self
    {
        if (!is_null($this->getId()) && $this->getId() !== $id) {
            throw new TargetException(sprintf('ID of the target is already defined to %s', $this->getId()));
        }
        $this->id = $id;

        return $this;
    }

    /**
     * Sets class name
     * @param SimpleModelInterface|null $model
     * @return Target
     */
    protected function setClassNameFromModel(?SimpleModelInterface $model): self
    {
        $this->setClassName($model ? get_class($model) : null);
        return $this;
    }

    /**
     * Sets id
     * @param SimpleModelInterface|null $model
     * @return Target
     */
    protected function setIdFromModel(?SimpleModelInterface $model): self
    {
        $this->setId($model ? $model->getId() : null);
        return $this;
    }

    /**
     * Refreshes Target data
     * @return Target
     */
    public function refresh(): self
    {
        $this->setIdFromModel($this->getModel());
        $this->setClassNameFromModel($this->getModel());
        return $this;
    }

    /**
     * Returns model of target
     * @param bool $throwExceptionOnNull
     * @return SimpleModelInterface|null
     */
    public function getModel(bool $throwExceptionOnNull = false): ?SimpleModelInterface
    {
        if (is_null($this->model) && $this->getClassName()) {
            $event = new LoadTargetModel($this);
            $this->eventDispatcher->dispatch($event);
            $this->model = $event->getModel();
        }
        if (!$this->model && $throwExceptionOnNull) {
            throw new InvalidTargetException(sprintf('Unable to load model of class %s', $this->getClassName()));
        }
        return $this->model;
    }

    /**
     * Returns model id
     * @return mixed
     */
    public function getModelId()
    {
        try {
            return $this->getModel(true)->getId();
        } catch (InvalidTargetException $e) {
            return null;
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'className' => $this->getClassName(),
            'id' => $this->getId(),
        ];
    }

    /**
     * Sets data from array
     * @param array $data
     * @return Target
     */
    public function fromArray(array $data): self
    {
        $this
            ->setClassName(isset($data['className']) ? $data['className'] : null)
            ->setId(isset($data['id']) ? $data['id'] : null)
        ;

        return $this;
    }
}