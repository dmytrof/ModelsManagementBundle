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

interface ArrayConvertibleModelInterface
{
    /**
     * Converts data to array
     * @return array
     */
    public function toArray(): array;

    /**
     * Sets data from array
     * @param array $data
     * @return ArrayConvertibleModelInterface
     */
    public function fromArray(array $data): self;
}