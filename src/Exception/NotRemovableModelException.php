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

use Dmytrof\ModelsManagementBundle\Model\ConditionalRemovalInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class NotRemovableModelException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * NotRemovableModelException constructor.
     * @param ConditionalRemovalInterface $entity
     * @param null $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(ConditionalRemovalInterface $entity, $message = null, $code = 0, Throwable $previous = null)
    {
        $message = $message ?? 'Deletion prohibited';
        parent::__construct($message, $code, $previous);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
        return 403;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return [];
    }
}