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

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class NotDeletableModelException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $headers = ['X-Conditional-Deletion-Error' => true];

    /**
     * NotDeletableModelException constructor.
     * @param null $message
     * @param null $statusCode
     * @param array $headers
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = null, $statusCode = null, array $headers = [], $code = 0, Throwable $previous = null)
    {
        $message = 'Deletion prohibited'. ($message ? ': '.$message : '');
        parent::__construct($message, $code, $previous);

        $this->statusCode = $statusCode ?? 403;
        $this->headers = array_merge($this->headers, $headers);
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}