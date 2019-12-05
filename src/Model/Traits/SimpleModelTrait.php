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

use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;

trait SimpleModelTrait
{
    /**
     * Returns id field name
     * @return string
     */
    protected function getIdFieldName(): string
    {
        return 'id';
    }

    /**
     * Returns id
     * @see SimpleModelInterface::getId()
     * @return mixed
     */
    public function getId()
    {
        return $this->{$this->getIdFieldName()};
    }

    /**
     * Sets id
     * @param mixed $id
     * @return SimpleModelInterface
     */
    public function setId($id): SimpleModelInterface
    {
        $this->{$this->getIdFieldName()} = $id;
        return $this;
    }

    /**
     * Checks if model is new
     * @see SimpleModelInterface::isNew()
     * @return bool
     */
    public function isNew(): bool
    {
        return is_null($this->getId());
    }

    /**
     * Returns title of the model
     * @return string
     */
    public function getTitleOfModel(): string
    {
        return 'ID: '.$this->getId();
    }

    public function __toString()
    {
        try {
            return $this->isNew() ? 'NEW' : $this->getTitleOfModel();
        } catch (\Exception $e) {
            return 'N\A';
        }
    }
}