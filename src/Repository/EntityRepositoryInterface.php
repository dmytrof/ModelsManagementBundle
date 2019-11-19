<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Repository;

use Dmytrof\ModelsManagementBundle\Model\{DoctrinePaginator, SimpleModelInterface};
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface EntityRepositoryInterface extends ObjectRepository
{
    /**
     * Returns alias
     * @param QueryBuilder|null $builder
     * @return string
     */
    public function getAlias(?QueryBuilder $builder = null): string;

    /**
     * Creates new entity
     * @return SimpleModelInterface
     */
    public function createNew(): SimpleModelInterface;

    /**
     * Returns query builder
     * @param array $options
     * @return QueryBuilder
     */
    public function getQueryBuilder(array $options = []): QueryBuilder;

    /**
     * Configures options for getQueryBuilder
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    public function configureGetQueryBuilderOptions(OptionsResolver $resolver): OptionsResolver;

    /**
     * Returns paginator
     * @param array $options
     * @return DoctrinePaginator
     */
    public function getPaginator(array $options = []): DoctrinePaginator;

    /**
     * Configures paginator options
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    public function configureGetPaginatorOptions(OptionsResolver $resolver): OptionsResolver;
}