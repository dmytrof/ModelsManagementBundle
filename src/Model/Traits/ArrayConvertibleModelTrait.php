<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Model\Traits;

use Dmytrof\ModelsManagementBundle\Model\ArrayConvertibleModelInterface;

trait ArrayConvertibleModelTrait
{
    /**
     * @see ArrayConvertibleModelInterface::toArray()
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @see ArrayConvertibleModelInterface::fromArray()
     * @param array $data
     * @return ArrayConvertibleModelInterface
     */
    public function fromArray(array $data): ArrayConvertibleModelInterface
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }
}