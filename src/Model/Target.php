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

use Doctrine\Common\Persistence\{ObjectRepository, ManagerRegistry};
use Symfony\Component\Validator\Constraints as Assert;
use Dmytrof\ModelsManagementBundle\{Exception\TargetException, Model\SimpleModelInterface, Repository\EntityRepositoryInterface};

class Target
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

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
     * @param ManagerRegistry $registry
     * @param SimpleModelInterface|null $target
     */
    public function __construct(ManagerRegistry $registry, SimpleModelInterface $target = null)
    {
        $this->setRegistry($registry);
        $this->setClassNameFromModel($target);
        $this->setIdFromModel($target);
        $this->model = $target;
    }

    /**
     * Sets doctrine
     * @param ManagerRegistry $registry
     * @return $this
     */
    public function setRegistry(ManagerRegistry $registry): self
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
    public function setClassName(?string $className): self
    {
        if (!is_null($this->getClassName())) {
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
    public function setId($id): self
    {
        if (!is_null($this->getId())) {
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
        $this->className = $model ? get_class($model) : null;
        return $this;
    }

    /**
     * Sets id
     * @param SimpleModelInterface|null $model
     * @return Target
     */
    protected function setIdFromModel(?SimpleModelInterface $model): self
    {
        $this->id = $model ? $model->getId() : null;
        return $this;
    }

    /**
     * Returns model of target
     * @param ManagerRegistry|null $registry
     * @return SimpleModelInterface|null
     */
    public function getModel(ManagerRegistry $registry = null): ?SimpleModelInterface
    {
        if (is_null($this->model) && $this->getClassName()) {
            $repo = $this->getTargetRepository($registry ?: $this->getRegistry());
            if (!is_null($this->getId())) {
                $this->model = $repo->find($this->getId());
            } else if ($repo instanceof EntityRepositoryInterface) {
                $this->model = $repo->createNew();
            } else {
                $className = $this->getClassName();
                $this->model = new $className();
            }
        }

        return $this->model;
    }

    /**
     * Returns target repository
     * @param ManagerRegistry|null $registry
     * @return ObjectRepository
     */
    public function getTargetRepository(ManagerRegistry $registry = null): ObjectRepository
    {
        $repository = null;
        if ($this->getClassName()) {
            $registry = $registry ?: $this->getRegistry();
            $repository = $registry->getRepository($this->getClassName());
        }
        if (!$repository) {
            throw new TargetException(sprintf('Undefined className'));
        }

        return $repository;
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