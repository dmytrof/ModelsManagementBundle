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
     * @return int
     */
    public function getPage(): int;

    /**
     * Returns limit (count on page)
     * @return int
     */
    public function getLimit(): int;

    /**
     * Returns total count
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @return array
     */
    public function getItems(): array;
}