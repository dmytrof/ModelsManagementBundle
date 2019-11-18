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

use Dmytrof\ModelsManagementBundle\Model\PaginatorInterface;

class Paginator implements PaginatorInterface
{
    /**
     * @var array
     */
    protected $items;

    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $totalCount = 0;

    /**
     * Paginator constructor.
     * @param iterable $items
     * @param int|null $page
     * @param int|null $limit
     * @param int|null $totalCount
     */
    public function __construct(iterable $items, ?int $page = 1, ?int $limit = null, ?int $totalCount = null)
    {
        $this->items = [];
        array_push($this->items, ...$items);
        $itemsCount = count($this->items);
        $this->page = $page;
        $this->limit = $limit ?: $itemsCount;
        $this->totalCount = $totalCount ?: $itemsCount;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getItems());
    }
}