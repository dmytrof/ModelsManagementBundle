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

use Doctrine\ORM\{EntityManagerInterface, Mapping\ClassMetadata};
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\{Form\FormFactoryInterface, Validator\Validator\ValidatorInterface};
use Symfony\Component\OptionsResolver\{Options, OptionsResolver};
use Dmytrof\ModelsManagementBundle\Model\{DoctrinePaginator, SimpleModelInterface};
use Dmytrof\ModelsManagementBundle\{Repository\EntityRepositoryInterface, Utils\OptionsFilter};

abstract class AbstractDoctrineManager extends AbstractManager
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * AbstractDoctrineManager constructor.
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
     * Disables doctrine filter
     * @param string $name
     * @return bool
     */
    protected function disableDoctrineFilter(string $name): bool
    {
        $this->getManager()->getFilters()->disable($name);
        return true;
    }

    /**
     * Enables doctrine filter
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
     * @param array $options
     * @return \Iterator
     */
    public function getIterator(array $options = []): \Iterator
    {
        $options = $this->configureGetIteratorOptions(new OptionsResolver())->resolve($options);

        $query = $this->getRepository()->getQueryBuilder((new OptionsFilter())->removeUndefinedOptions($options, $this->getRepository()->configureGetQueryBuilderOptions(new OptionsResolver())))
            ->setFirstResult(($options['page']-1) * $options['limit'])
            ->setMaxResults($options['limit'])
            ->getQuery()
        ;
        return $query->iterate();
    }

    /**
     * Configures paginator options
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    public function configureGetIteratorOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'filter' => [],
            'sorting' => [],
            'page' => 1,
            'limit' => 50,
        ]);

        $resolver
            ->setAllowedTypes('filter', ['array'])
            ->setAllowedTypes('sorting', ['array'])
        ;

        $resolver
            ->setNormalizer('page', function (Options $options, $page) {
                return $page > 0 ? (int) $page : 1;
            })
            ->setNormalizer('limit', function (Options $options, $limit) {
                return $limit > 0 ? (int) $limit : 50;
            })
        ;

        return $resolver;
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
    public function getDoctrinePaginator(array $options = []): DoctrinePaginator
    {
        return $this->getRepository()->getPaginator($options);
    }
}