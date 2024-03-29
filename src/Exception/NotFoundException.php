<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Exception;

use Dmytrof\ModelsManagementBundle\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class NotFoundException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return 404;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return [];
    }
}
