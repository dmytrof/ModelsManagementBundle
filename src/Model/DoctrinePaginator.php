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
use Doctrine\ORM\Tools\Pagination\Paginator as BasePaginator;

class DoctrinePaginator extends BasePaginator implements PaginatorInterface
{
    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $limit = 50;

    /**
     * Updates query with page limits
     * @return DoctrinePaginator
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
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Sets page
     * @param int $page
     * @return DoctrinePaginator
     */
    public function setPage(int $page): self
    {
        $this->page = $page;
        $this->updateQueryPage();
        return $this;
    }

    /**
     * Returns limit
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Sets limit
     * @param int $limit
     * @return DoctrinePaginator
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
        return $this->getIterator()->getArrayCopy();
    }
}