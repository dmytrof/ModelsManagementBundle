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
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface EntityRepositoryInterface extends ObjectRepository
{
    /**
     * Returns alias
     */
    public function getAlias(?QueryBuilder $builder = null): string;

    /**
     * Creates new entity
     */
    public function createNew(): SimpleModelInterface;

    /**
     * Returns query builder
     */
    public function getQueryBuilder(array $options = []): QueryBuilder;

    /**
     * Configures options for getQueryBuilder
     */
    public function configureGetQueryBuilderOptions(OptionsResolver $resolver): OptionsResolver;

    /**
     * Returns paginator
     */
    public function getPaginator(array $options = []): DoctrinePaginator;

    /**
     * Configures paginator options
     */
    public function configureGetPaginatorOptions(OptionsResolver $resolver): OptionsResolver;
}