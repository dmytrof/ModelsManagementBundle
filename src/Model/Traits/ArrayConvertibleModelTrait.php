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
use Doctrine\Inflector\{Inflector, NoopWordInflector};

trait ArrayConvertibleModelTrait
{
    /**
     * @see ArrayConvertibleModelInterface::toArray()
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        $inflector = (new Inflector(new NoopWordInflector(), new NoopWordInflector()));
        foreach (get_object_vars($this) as $property => $value) {
            $method = 'toArray'.$inflector->classify($property);
            if (method_exists($this, $method)) {
                $array[$property] = $this->$method($value);
            } else if (is_scalar($value) || is_array($value)) {
                $array[$property] = $value;
            } elseif ($value instanceof \StdClass) {
                $array[$property] = (array) $value;
            } elseif ($value instanceof ArrayConvertibleModelInterface) {
                $array[$property] = $value->toArray();
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
        $inflector = (new Inflector(new NoopWordInflector(), new NoopWordInflector()));
        foreach ($data as $key => $value) {
            $method = 'fromArray'.$inflector->classify($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }

        return $this;
    }
}