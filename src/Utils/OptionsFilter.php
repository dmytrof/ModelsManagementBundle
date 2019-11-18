<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Utils;

use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionsFilter
{
    /**
     * Removes undefined options from array of options according to OptionsResolver
     * @param array $options
     * @param OptionsResolver $resolver
     * @return array
     */
    public function removeUndefinedOptions(array $options, OptionsResolver $resolver): array
    {
        return array_intersect_key($options, array_flip($resolver->getDefinedOptions()));
    }
}