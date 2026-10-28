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
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\{Form\FormFactoryInterface, Validator\Validator\ValidatorInterface};
use Symfony\Component\OptionsResolver\{Options, OptionsResolver};
use Dmytrof\ModelsManagementBundle\Model\{DoctrinePaginator, SimpleModelInterface};
use Dmytrof\ModelsManagementBundle\{Exception\RuntimeException,
    Repository\EntityRepositoryInterface,
    Utils\OptionsFilter};

abstract class AbstractDoctrineManager extends AbstractManager
{
    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry, ValidatorInterface $validator, FormFactoryInterface $formFactory)
    {
        parent::__construct($validator, $formFactory);
        $this->registry = $registry;
    }

    /**
     * Returns Registry
     */
    public function getRegistry(): ManagerRegistry
    {
        return $this->registry;
    }

    /**
     * Returns entity manager
     */
    public function getManager(): EntityManagerInterface
    {
        return $this->getRegistry()->getManager();
    }

    /**
     * Returns repository for entity
     */
    public function getRepository(): EntityRepositoryInterface
    {
        return $this->getRegistry()->getRepository($this->getModelClass());
    }

    /**
     * Configures options for save
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
            if (!$this->getManager()->isOpen()) {
                $this->getRegistry()->resetManager();
            }

            throw $exception;
        }

        return $this;
    }

    /**
     * Configures options for remove
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
     * Checks if model can be deleted
     */
    protected function canModelBeDeleted(SimpleModelInterface $model): bool
    {
        // Deletion check is on preRemove doctrine event
        return true;
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
    public function get(null|int|string $id): ?SimpleModelInterface
    {
        $object = $this->getRepository()->find($id);
        if ($object && !$object instanceof SimpleModelInterface) {
            throw new RuntimeException('Model is not instance of SimpleModelInterface');
        }

        return $object;
    }

    /**
     * {@inheritDoc}
     */
    public function new(): SimpleModelInterface
    {
        return $this->getRepository()->createNew(...func_get_args());
    }

    /**
     * Disables doctrine filter
     */
    protected function disableDoctrineFilter(string $name): bool
    {
        $this->getManager()->getFilters()->disable($name);
        return true;
    }

    /**
     * Enables doctrine filter
     */
    protected function enableDoctrineFilter(string $name): bool
    {
        $this->getManager()->getFilters()->enable($name);
        return true;
    }

    /**
     * Returns entities iterator
     */
    public function getIterator(array $options = []): iterable
    {
        $options = $this->configureGetIteratorOptions(new OptionsResolver())->resolve($options);

        $query = $this->getRepository()->getQueryBuilder((new OptionsFilter())->removeUndefinedOptions($options, $this->getRepository()->configureGetQueryBuilderOptions(new OptionsResolver())))
            ->setFirstResult(($options['page']-1) * $options['limit'])
            ->setMaxResults($options['limit'])
            ->getQuery()
        ;
        return $query->toIterable();
    }

    /**
     * Configures paginator options
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
     */
    public function getClassMetadata(): ClassMetadata
    {
        return $this->getManager()->getClassMetadata(static::MODEL_CLASS);
    }

    /**
     * Returns paginator
     */
    public function getDoctrinePaginator(array $options = []): DoctrinePaginator
    {
        return $this->getRepository()->getPaginator($options);
    }
}
