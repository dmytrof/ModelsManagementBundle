<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Utils;

use Dmytrof\ModelsManagementBundle\Utils\OptionsFilter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OptionsFilterTest extends TestCase
{
    public function testRemoveUndefinedOptions(): void
    {
        $optionsFilter = new OptionsFilter();

        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'foo' => 'bar',
            'test' => null
        ]);

        $this->assertEquals(['foo' => 'voo'], $optionsFilter->removeUndefinedOptions(['foo' => 'voo', 'qwe' => 'asd'], $resolver));

        $this->assertEquals([], $optionsFilter->removeUndefinedOptions(['moo' => 'voo', 'qwe' => 'asd'], $resolver));
    }
}