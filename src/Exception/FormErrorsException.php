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
        $getPropertyPath = function(FormError $error, FormInterface $form) {
            $path = $error->getCause() && method_exists($error->getCause(), 'getPropertyPath') ? $error->getCause()->getPropertyPath() : null;
            if (is_null($path) && $error->getOrigin()->getPropertyPath()) {
                $pathParts = [];
                $form = $error->getOrigin();
                while ($form->getParent()) {
                    array_unshift($pathParts, $form->getPropertyPath());
                    $form = $form->getParent();
                }
                $path = join('.', $pathParts);
            }
            $path = preg_replace('/children\[([^\]]+)\]/', '${1}', $path);
            $path = preg_replace('/\[(\d+)\]/', '.${1}', $path);
            if (substr($path, 0, 5) === 'data.') {
                $path = substr($path, 5);
            }
            if (substr($path, -5) === '.data') {
                $path = substr($path, 0,-5);
            }
            return $path;
        };
        $form = $this->getForm();
        foreach ($form->getErrors(true, true) as $error) {
            $path = $getPropertyPath->call($this, $error, $form);
            if (!isset($errors[$path])) {
                $errors[$path] = [];
            }
            $errors[$path][] = $error->getMessage();
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
