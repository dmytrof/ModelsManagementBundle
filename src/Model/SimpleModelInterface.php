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

interface SimpleModelInterface
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
     * Returns title of the model
     * @return string
     */
    public function getModelTitle(): string;

    /**
     * Returns code of the model
     * @return string
     */
    public function getModelCode(): string;
}