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

class Paginator implements PaginatorInterface
{
    protected array $items;

    protected int $page;

    protected int $limit;

    protected int $totalCount;

    public function __construct(iterable $items, ?int $page = null, ?int $limit = null, ?int $totalCount = null)
    {
        $this->items = [];
        array_push($this->items, ...$items);
        $itemsCount = count($this->items);
        $this->page = $page ?: 1;
        $this->limit = $limit ?: $itemsCount;
        $this->totalCount = $totalCount ?: $itemsCount;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->getItems());
    }
}