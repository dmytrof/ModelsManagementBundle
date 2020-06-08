<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Repository\Traits;

use Dmytrof\ModelsManagementBundle\Model\{DoctrinePaginator, SimpleModelInterface};
use Dmytrof\ModelsManagementBundle\{Repository\EntityRepositoryInterface, Utils\OptionsFilter};
use Doctrine\ORM\{Event\LifecycleEventArgs, Events, QueryBuilder};
use Symfony\Component\OptionsResolver\{OptionsResolver, Options};

trait EntityRepositoryTrait
{
    /**
     * Alias for entity in query
     * @var string
     */
    protected $alias;

    /**
     * Generates random key
     * @param int $length
     * @return string
     */
    public function generateRandomKey(int $length = 5): string
    {
        try {
            $source = random_bytes($length * 2);
        } catch (\Exception $e) {
            $source = rand(pow(10, $length * 2), pow(10, $length * 2 + 1) - 1);
        }

        return substr(preg_replace('/[^A-z]/', '', base64_encode($source)), 0, $length);
    }


    /**
     * Returns root alias
     * @see EntityRepositoryInterface::getAlias()
     * @param QueryBuilder|null $builder
     * @return string
     */
    public function getAlias(?QueryBuilder $builder = null): string
    {
        if ($builder) {
            return $builder->getRootAliases()[0];
        }
        if (is_null($this->alias)) {
            $this->alias = $this->generateRandomKey();
        }

        return $this->alias;
    }

    /**
     * Creates new entity
     * @see EntityRepositoryInterface::createNew()
     * @return SimpleModelInterface
     */
    public function createNew(): SimpleModelInterface
    {
        $className = $this->getClassName();
        $object = new $className(...func_get_args());

        $this->getEntityManager()->getEventManager()->dispatchEvent(Events::postLoad, new LifecycleEventArgs($object, $this->getEntityManager()));

        return $object;
    }

    /**
     * Applies filter to query builder
     * @param QueryBuilder $builder
     * @param array $filters
     */
    public function applyFilter(QueryBuilder $builder, array $filters = []): void
    {

    }

    /**
     * Applies sorting to query builder
     * @param QueryBuilder $builder
     * @param array $sorting
     */
    public function applySorting(QueryBuilder $builder, array $sorting = []): void
    {

    }

    /**
     * Configures options for getQueryBuilder
     * @see EntityRepositoryInterface::configureGetQueryBuilderOptions()
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    public function configureGetQueryBuilderOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'alias' => $this->getAlias(),
            'filter' => [],
            'sorting' => [],
        ]);
        $resolver
            ->setAllowedTypes('alias', ['string'])
            ->setAllowedTypes('filter', ['array'])
            ->setAllowedTypes('sorting', ['array'])
        ;

        return $resolver;
    }

    /**
     * Returns query builder
     * @see EntityRepositoryInterface::getQueryBuilder()
     * @param array $options
     * @return QueryBuilder
     */
    public function getQueryBuilder(array $options = []): QueryBuilder
    {
        $options = $this->configureGetQueryBuilderOptions(new OptionsResolver())->resolve($options);

        /** @var QueryBuilder $builder */
        $builder = $this->createQueryBuilder($options['alias']);

        $this->applyFilter($builder, $options['filter']);
        $this->applySorting($builder, $options['sorting']);

        return $builder;
    }

    /**
     * Configures paginator options
     * @see EntityRepositoryInterface::configureGetPaginatorOptions()
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    public function configureGetPaginatorOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'filter' => [],
            'sorting' => [],
            'page' => 1,
            'limit' => 50,
            'fetchJoinCollection' => true,
            'queryBuilderCallback' => null,
        ]);

        $resolver
            ->setAllowedTypes('filter', 'array')
            ->setAllowedTypes('sorting', 'array')
            ->setAllowedTypes('fetchJoinCollection', 'bool')
            ->setAllowedTypes('queryBuilderCallback', ['null', \Closure::class])
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
     * Returns paginator
     * @see EntityRepositoryInterface::getPaginator()
     * @param array $options
     * @return DoctrinePaginator
     */
    public function getPaginator(array $options = []): DoctrinePaginator
    {
        $options = $this->configureGetPaginatorOptions(new OptionsResolver())->resolve($options);

        $queryBuilder = $this->getQueryBuilder((new OptionsFilter())->removeUndefinedOptions($options, $this->configureGetQueryBuilderOptions(new OptionsResolver())));
        if ($options['queryBuilderCallback']) {
            $options['queryBuilderCallback']->call($this, $queryBuilder);
        }

        return (new DoctrinePaginator($queryBuilder, $options['fetchJoinCollection']))
            ->setPage($options['page'])
            ->setLimit($options['limit'])
        ;
    }
}