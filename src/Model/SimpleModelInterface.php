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

interface SimpleModelInterface extends DefinedModelInterface
{
    /**
     * Returns model id
     * @return mixed
     */
    public function getId();

    /**
     * Checks if model is new
     * @return bool
     */
    public function isModelNew(): bool;

    /**
     * Returns title of the model object
     * @return string
     */
    public function getModelTitle(): string;
}