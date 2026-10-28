<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Manager;

use Dmytrof\ModelsManagementBundle\Exception\NotFoundException;
use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;

interface ManagerInterface
{
    /**
     * Returns managed model's class
     */
    public function getModelClass(): string;

    /**
     * Saves model
     */
    public function save(SimpleModelInterface $model, array $options = []): self;

    /**
     * Removes model
     */
    public function remove(SimpleModelInterface $model, array $options = []): self;

    /**
     * Returns model. Null if model not exists
     */
    public function get(null|int|string $id): ?SimpleModelInterface;

    /**
     * Returns model. Throws NotFoundException if model not exists
     */
    public function getItem(null|int|string$id): SimpleModelInterface;

    /**
     * Creates new model and returns it
     */
    public function new(): SimpleModelInterface;
}