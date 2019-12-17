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

use Dmytrof\ModelsManagementBundle\Manager\AbstractManager;
use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;
use Dmytrof\ModelsManagementBundle\Tests\Data\{SomeModel, SomeModelType};

class SomeModelManager extends AbstractManager
{
    const MODEL_CLASS = SomeModel::class;
    const FORM_TYPE_CREATE_ITEM = SomeModelType::class;

    /**
     * @inheritDoc
     */
    protected function _save(SimpleModelInterface $model, array $options = [])
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function _remove(SimpleModelInterface $model, array $options = [])
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function get($id): ?SimpleModelInterface
    {
        return $id == 1 ? (new SomeModel())->setId(1) : null;
    }

    /**
     * @inheritDoc
     */
    public function new(): SimpleModelInterface
    {
        return new SomeModel();
    }
}