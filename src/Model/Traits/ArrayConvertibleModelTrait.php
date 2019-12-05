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
use Doctrine\Common\Inflector\Inflector;

trait ArrayConvertibleModelTrait
{
    /**
     * @see ArrayConvertibleModelInterface::toArray()
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach (get_object_vars($this) as $name => $value) {
            if (is_scalar($value) || is_array($value)) {
                $array[$name] = $value;
            } elseif ($value instanceof \StdClass) {
                $array[$name] = (array) $value;
            } elseif ($value instanceof ArrayConvertibleModelInterface) {
                $array[$name] = $value->toArray();
            }
        }

        return $array;
    }

    /**
     * @see ArrayConvertibleModelInterface::fromArray()
     * @param array $data
     * @return ArrayConvertibleModelInterface
     */
    public function fromArray(array $data): ArrayConvertibleModelInterface
    {
        foreach ($data as $key => $value) {
            $method = 'fromArraySet'.Inflector::classify($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }
}