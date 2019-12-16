<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Data;

use Dmytrof\ModelsManagementBundle\Model\{ConditionalRemovalInterface, SimpleModelInterface, Traits\SimpleModelTrait};

class SomeModel implements SimpleModelInterface, ConditionalRemovalInterface
{
    use SimpleModelTrait;

    protected $id;

    /**
     * @var string
     */
    protected $foo;

    /**
     * Returns foo
     * @return string|null
     */
    public function getFoo(): ?string
    {
        return $this->foo;
    }

    /**
     * Sets foo
     * @param string|null $foo
     * @return SomeModel
     */
    public function setFoo(?string $foo): self
    {
        $this->foo = $foo;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function canBeRemoved(): bool
    {
        return (bool) $this->getId();
    }
}