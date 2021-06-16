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

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

trait DefinedModelTrait
{
    /**
     * Returns code of the class
     * @return string
     */
    public static function getClassCode(): string
    {
        return substr(strrchr(static::class, '\\'), 1);
    }

    /**
     * Returns code of the model
     * @return string
     */
    public static function getClassName(): string
    {
        return ucwords(str_replace('_', ' ', InflectorFactory::create()->build()->tableize(static::getClassCode())));
    }
}