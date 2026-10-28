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

use Doctrine\ORM\Tools\Pagination\Paginator as BasePaginator;

class DoctrinePaginator extends BasePaginator implements PaginatorInterface
{
    protected int $page = 1;

    protected int $limit = 50;

    /**
     * Updates query with page limits
     */
    protected function updateQueryPage(): self
    {
        $this->getQuery()
            ->setFirstResult(($this->getPage() - 1) * $this->getLimit())
            ->setMaxResults($this->getLimit())
        ;
        return $this;
    }

    /**
     * Returns page
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Sets page
     */
    public function setPage(int $page): self
    {
        $this->page = $page;
        $this->updateQueryPage();
        return $this;
    }

    /**
     * Returns limit
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Sets limit
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        $this->updateQueryPage();
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTotalCount(): int
    {
        return count($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getItems(): array
    {
        return iterator_to_array($this->getIterator(), false);
    }
}
