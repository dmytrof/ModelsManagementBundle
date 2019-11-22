<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Model;

use Dmytrof\ModelsManagementBundle\Model\Paginator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{
    public function testArrayPaginator(): void
    {
        $items = ['foo', 'bar', 'baz'];
        $paginator = new Paginator($items);

        $this->assertEquals($items, $paginator->getItems());
        $this->assertInstanceOf(\Iterator::class, $paginator->getIterator());
        $this->assertEquals(1, $paginator->getPage());
        $this->assertEquals(3, $paginator->getLimit());
        $this->assertEquals(3, $paginator->getTotalCount());

        $paginator = new Paginator($items, 2, 3, 77);

        $this->assertEquals($items, $paginator->getItems());
        $this->assertEquals(2, $paginator->getPage());
        $this->assertEquals(3, $paginator->getLimit());
        $this->assertEquals(77, $paginator->getTotalCount());
    }

    public function testCollectionPaginator(): void
    {
        $items = new ArrayCollection(['foo', 'bar', 'baz']);
        $paginator = new Paginator($items);

        $this->assertEquals($items->toArray(), $paginator->getItems());
        $this->assertEquals(1, $paginator->getPage());
        $this->assertEquals(3, $paginator->getLimit());
        $this->assertEquals(3, $paginator->getTotalCount());
    }

    public function testIterablePaginator(): void
    {
        $items = new \ArrayIterator(['foo', 'bar', 'baz']);
        $paginator = new Paginator($items);

        $this->assertEquals($items->getArrayCopy(), $paginator->getItems());
        $this->assertEquals(1, $paginator->getPage());
        $this->assertEquals(3, $paginator->getLimit());
        $this->assertEquals(3, $paginator->getTotalCount());
    }
}