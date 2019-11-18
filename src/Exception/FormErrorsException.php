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

use Symfony\Component\Form\{FormError, FormInterface};
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class FormErrorsException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * FormErrorsException constructor.
     * @param FormInterface $form
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(FormInterface $form, string $message = "Form data errors", int $code = 0, Throwable $previous = null)
    {
        $this->form = $form;
        parent::__construct($message.': '.json_encode($this->getFormErrors()), $code, $previous);
    }

    /**
     * Returns form
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        return $this->form;
    }

    /**
     * Returns form errors as array
     * @return array
     */
    public function getFormErrors(): array
    {
        $errors = [];
        $getPropertyPath = function(FormError $error) {
            $path = $error->getCause() ? $error->getCause()->getPropertyPath() : null;
            if (substr($path, 0, 1) == '[') { // Validated by form
                $path = $error->getOrigin()->getPropertyPath().'.'.trim(str_replace('].children[', '.', $path), '[]');
            }
            return $path;
        };
        foreach ($this->getForm()->getErrors(true, true ) as $error) {
            $_errors = &$errors;
            foreach (explode('.', $getPropertyPath->call($this, $error)) as $pathPart) {
                if (!isset($_errors[$pathPart])) {
                    $_errors[$pathPart] = [];
                }
                $_errors = &$_errors[$pathPart];
            }
            $_errors[] = $error->getMessage();
        }
        return $errors;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode(): int
    {
        return 400;
    }

    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return [];
    }
}