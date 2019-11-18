<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\{
    Form\FormFactoryInterface,
    OptionsResolver\OptionsResolver,
    Validator\Validator\ValidatorInterface};
use Dmytrof\ModelsManagementBundle\Model\{DoctrinePaginator, SimpleModelInterface};

abstract class AbstractDoctrineManager extends AbstractManager
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * AbstractDoctrinePaginationManager constructor.
     * @param RegistryInterface $registry
     * @param ValidatorInterface $validator
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(RegistryInterface $registry, ValidatorInterface $validator, FormFactoryInterface $formFactory)
    {
        parent::__construct($validator, $formFactory);
        $this->registry = $registry;
    }

    /**
     * Returns Registry
     * @return RegistryInterface
     */
    public function getRegistry(): RegistryInterface
    {
        return $this->registry;
    }

    /**
     * Returns entity manager
     * @return EntityManagerInterface
     */
    public function getManager(): EntityManagerInterface
    {
        return $this->getRegistry()->getEntityManager();
    }

    /**
     * Configures options for save
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureSaveOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureSaveOptions($resolver);
        $resolver->setDefaults([
            'flush'     => true,
        ]);
        $resolver->setAllowedTypes('flush',     'bool');

        return $resolver;
    }

    /**
     * preSave model handler
     * @param SimpleModelInterface $entity
     * @param array $options
     * @return AbstractDoctrineManager
     */
    protected function preSave(SimpleModelInterface $entity, array $options = []): self
    {
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function _save(SimpleModelInterface $model, array $options = []): self
    {
        $options = $this->configureSaveOptions(new OptionsResolver())->resolve($options);

        try {
            $this->preSave($model, $options);

            $this->getManager()->persist($model);
            if ($options['flush']) {
                $this->getManager()->flush();
            }

        } catch (\Throwable $exception) {
            if (!$this->getRegistry()->getEntityManager()->isOpen()) {
                $this->getRegistry()->resetManager();
            }

            throw $exception;
        }

        return $this;
    }

    /**
     * Configures options for remove
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureRemoveOptions(OptionsResolver $resolver): OptionsResolver
    {
        parent::configureRemoveOptions($resolver);
        $resolver->setDefaults([
            'flush'     => true,
        ]);
        $resolver->setAllowedTypes('flush',     'bool');

        return $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function _remove(SimpleModelInterface $model, array $options = []): ManagerInterface
    {
        $this->getManager()->remove($model);
        if ($options['flush']) {
            $this->getManager()->flush();
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id): ?SimpleModelInterface
    {
        return $this->getRepository()->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function new(): SimpleModelInterface
    {
        return $this->getRepository()->createNew(...func_get_args());
    }

    /**
     * Returns repository for entity
     * @return EntityRepositoryInterface
     */
    public function getRepository(): EntityRepositoryInterface
    {
        return $this->getManager()->getRepository($this->getModelClass());
    }

    /**
     * Disables EM filter
     * @param string $name
     * @return bool
     */
    protected function disableDoctrineFilter(string $name): bool
    {
        $this->getManager()->getFilters()->disable($name);
        return true;
    }

    /**
     * Enables EM filter
     * @param string $name
     * @return bool
     */
    protected function enableDoctrineFilter(string $name): bool
    {
        $this->getManager()->getFilters()->enable($name);
        return true;
    }

    /**
     * Returns entities iterator
     * @param int $page
     * @param int $onPage
     * @param array $filter
     * @param array $sorting
     * @param array $settings
     * @return \Iterator
     */
    public function getIterator(int $page = 1, int $onPage = 10, array $filter = [], array $sorting = [], array $settings = []): \Iterator
    {
        $query = $this->getRepository()->getPaginationQueryBuilder($filter, $sorting, $settings)
            ->setFirstResult(($page-1) * $onPage)
            ->setMaxResults($onPage)
            ->getQuery()
        ;
        return $query->iterate();
    }

    /**
     * Returns class metadata
     * @return ClassMetadata
     */
    public function getClassMetadata(): ClassMetadata
    {
        return $this->getManager()->getClassMetadata(static::MODEL_CLASS);
    }

    /**
     * Returns paginator
     * @param array $options
     * @return DoctrinePaginator
     */
    public function getDoctrinePaginator(array $options): DoctrinePaginator
    {
        return $this->getRepository()->getPaginator($options);
    }
}