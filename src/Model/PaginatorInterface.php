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

interface PaginatorInterface extends \IteratorAggregate
{
    /**
     * Returns page
     */
    public function getPage(): int;

    /**
     * Returns limit (count on page)
     */
    public function getLimit(): int;

    /**
     * Returns total count
     */
    public function getTotalCount(): int;

    public function getItems(): array;
}