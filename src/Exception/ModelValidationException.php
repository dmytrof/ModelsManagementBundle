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

use Dmytrof\ModelsManagementBundle\Exception\ModelException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ModelValidationException extends ModelException
{
    /**
     * ModelValidationException constructor.
     * @param ConstraintViolationListInterface $errors
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(ConstraintViolationListInterface $errors, $message = "Validation error: %s", $code = 0, \Throwable $previous = null)
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            array_push($errorMessages, $error->getPropertyPath().' - '.$error->getMessage());
        }
        parent::__construct(sprintf($message, join('; ', $errorMessages)), $code, $previous);
    }
}
