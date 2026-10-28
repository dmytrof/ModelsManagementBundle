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
    #[Assert\NotNull]
    protected ?string $className = null;

    #[Assert\NotNull]
    protected mixed $id = null;

    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected ?SimpleModelInterface $model = null,
    ) {
        $this->refresh();
    }

    /**
     * Returns event dispatcher
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * Returns class name of target
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * Sets class name
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
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * Sets id
     */
    protected function setId(mixed $id): self
    {
        if (!is_null($this->getId()) && $this->getId() !== $id) {
            throw new TargetException(sprintf('ID of the target is already defined to %s', $this->getId()));
        }
        $this->id = $id;

        return $this;
    }

    /**
     * Sets class name
     */
    protected function setClassNameFromModel(?SimpleModelInterface $model): self
    {
        $this->setClassName($model ? get_class($model) : null);
        return $this;
    }

    /**
     * Sets id
     */
    protected function setIdFromModel(?SimpleModelInterface $model): self
    {
        $this->setId($model ? $model->getId() : null);
        return $this;
    }

    /**
     * Refreshes Target data
     */
    public function refresh(): self
    {
        $this->setIdFromModel($this->getModel());
        $this->setClassNameFromModel($this->getModel());
        return $this;
    }

    /**
     * Returns model of target
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
     */
    public function getModelId(): mixed
    {
        try {
            return $this->getModel(true)->getId();
        } catch (InvalidTargetException $e) {
            return null;
        }
    }

    public function toArray(): array
    {
        return [
            'className' => $this->getClassName(),
            'id' => $this->getId(),
        ];
    }

    /**
     * Sets data from array
     */
    public function fromArray(array $data): self
    {
        $this
            ->setClassName($data['className'] ?? null)
            ->setId($data['id'] ?? null)
        ;

        return $this;
    }
}
