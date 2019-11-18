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
     * @return string
     */
    public function getModelClass(): string;

    /**
     * Saves model
     * @param SimpleModelInterface $model
     * @param array $options
     * @return ManagerInterface
     */
    public function save(SimpleModelInterface $model, array $options = []): self;

    /**
     * Removes model
     * @param SimpleModelInterface $model
     * @param array $options
     * @return ManagerInterface
     */
    public function remove(SimpleModelInterface $model, array $options = []): self;

    /**
     * Returns model. Null if model not exists
     * @param int|string|null $id
     * @return SimpleModelInterface|null
     */
    public function get($id): ?SimpleModelInterface;

    /**
     * Returns model. Throws NotFoundException if model not exists
     * @param int|string|null $id
     * @return SimpleModelInterface
     * @throws NotFoundException
     */
    public function getItem($id): SimpleModelInterface;

    /**
     * Creates new model and returns it
     * @return SimpleModelInterface
     */
    public function new(): SimpleModelInterface;
}