<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Manager;

use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\{FormError, FormFactoryInterface, FormInterface};
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\{Options, OptionsResolver};
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Dmytrof\ModelsManagementBundle\Utils\OptionsFilter;
use Dmytrof\ModelsManagementBundle\Exception\{ManagerException,
    FormErrorsException,
    NotFoundException,
    ModelValidationException,
    NotDeletableModelException};
use Dmytrof\ModelsManagementBundle\Model\{SimpleModelInterface, ConditionalDeletionInterface};

abstract class AbstractManager implements ManagerInterface
{
    const MODEL_CLASS = null;
    const EXCEPTION_CLASS_NOT_FOUND = NotFoundException::class;
    const FORM_TYPE_CREATE_ITEM = null;
    const FORM_TYPE_UPDATE_ITEM = null;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * AbstractManager constructor.
     * @param ValidatorInterface $validator
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(ValidatorInterface $validator, FormFactoryInterface $formFactory)
    {
        $this->validator = $validator;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getModelClass(): string
    {
        $modelClass = static::MODEL_CLASS;
        if (!is_string($modelClass) || !strlen($modelClass)) {
            throw new ManagerException(sprintf('Undefined constant MODEL_CLASS for %s', get_class($this)));
        }

        return $modelClass;
    }

    /**
     * Returns validator
     * @return ValidatorInterface
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * Returns FormFactory
     * @return FormFactoryInterface
     */
    public function getFormFactory(): FormFactoryInterface
    {
        return $this->formFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getItem($id): SimpleModelInterface
    {
        $entity = !is_null($id) ? $this->get($id) : null;
        if (!$entity) {
            $class = static::EXCEPTION_CLASS_NOT_FOUND;
            $modelClassName = 'Model';
            if (is_subclass_of($this->getModelClass(), SimpleModelInterface::class)) {
                $modelClassName = call_user_func([$this->getModelClass(), 'getClassName']);
            }
            throw new $class(sprintf('%s not found', $modelClassName));
        }

        return $entity;
    }

    /**
     * Configures options for save
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureSaveOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'validate'  => false,
        ]);
        $resolver->setAllowedTypes('validate',  'bool');

        return $resolver;
    }

    /**
     * Saves model
     * @param SimpleModelInterface $model
     * @param array $options
     * @return mixed
     */
    protected abstract function _save(SimpleModelInterface $model, array $options = []);

    /**
     * {@inheritDoc}
     */
    public function save(SimpleModelInterface $model, array $options = []): ManagerInterface
    {
        $options = $this->configureSaveOptions(new OptionsResolver())->resolve($options);

        if ($options['validate']) {
            $errors = $this->getValidator()->validate($model);
            if ($errors->count()) {
                throw new ModelValidationException($errors);
            }
        }
        $this->_save($model, $options);

        return $this;
    }

    /**
     * Configures options for remove
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureRemoveOptions(OptionsResolver $resolver): OptionsResolver
    {
        return $resolver;
    }

    /**
     * Removes model
     * @param SimpleModelInterface $model
     * @param array $options
     * @return mixed
     */
    protected abstract function _remove(SimpleModelInterface $model, array $options = []);

    /**
     * {@inheritDoc}
     */
    public function remove(SimpleModelInterface $model, array $options = []): ManagerInterface
    {
        $options = $this->configureRemoveOptions(new OptionsResolver())->resolve($options);
        if (!$this->canModelBeDeleted($model)) {
            throw new NotDeletableModelException();
        }
        $this->_remove($model, $options);

        return $this;
    }

    /**
     * Checks if model can be deleted
     * @param SimpleModelInterface $model
     * @return bool
     */
    protected function canModelBeDeleted(SimpleModelInterface $model): bool
    {
        if ($model instanceof ConditionalDeletionInterface && !$model->canBeDeleted()) {
            throw new NotDeletableModelException();
        }

        return true;
    }

    /**
     * Removes model by id
     * @param $id
     * @param array $options
     * @return ManagerInterface
     */
    public function removeById($id, array $options = []): ManagerInterface
    {
        return $this->remove($this->getItem($id), $options);
    }

    /**
     * Configures options for get form
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureGetFormOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'model' => null,
            'requestMethod' => Request::METHOD_POST,
            'formOptions'   => [],
        ]);
        $resolver->setRequired('formClass');

        $resolver->setAllowedTypes('formClass',     'string');
        $resolver->setAllowedTypes('formOptions',   'array');

        $resolver->setAllowedValues('requestMethod', [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH]);

        $resolver->setNormalizer('formOptions', function (Options $options, $formOptions) {
            return array_merge($formOptions, ['method' => $options['requestMethod']]);
        });

        return $resolver;
    }

    /**
     * Returns Form
     * @param array $options
     * @return FormInterface
     */
    public function getForm(array $options = []): FormInterface
    {
        $options = $this->configureGetFormOptions(new OptionsResolver())->resolve($options);

        return $this->getFormFactory()->create($options['formClass'], $options['model'], $options['formOptions']);
    }

    /**
     * Returns form type class for createModel
     * @return string
     */
    public function getCreateModelFormType(): string
    {
        $formType = static::FORM_TYPE_CREATE_ITEM;
        if (!$formType) {
            throw new ManagerException(sprintf('Undefined constant FORM_TYPE_CREATE_ITEM for %s.', get_class($this)));
        }

        return $formType;
    }

    /**
     * Configures options for create from data
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureUpdateModelDataOptions(OptionsResolver $resolver): OptionsResolver
    {
        $this->configureGetFormOptions($resolver);
        $this->configureProcessModelFormOptions($resolver);

        $resolver
            ->setDefault('saveOptions', [])
            ->setAllowedTypes('saveOptions', 'array')
            ->setAllowedTypes('model',        ['null', $this->getModelClass()]);
        ;

        return $resolver;
    }

    /**
     * Applies before form handling
     * @param SimpleModelInterface $model
     * @param array $options
     */
    protected function preUpdateModelData(SimpleModelInterface $model, array $options)
    {
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function updateModelData(array $options)
    {
        $options = $this->configureUpdateModelDataOptions(new OptionsResolver())->resolve($options);

        $model = $options['model'] ?: $this->new();

        $this->preUpdateModelData($model, $options);
        $form = $this
            ->getForm((new OptionsFilter())->removeUndefinedOptions(array_merge($options, ['model' => $model]), $this->configureGetFormOptions(new OptionsResolver())))
        ;
        $form->setData($model);

        $this
            ->processModelForm($form, (new OptionsFilter())->removeUndefinedOptions($options, $this->configureProcessModelFormOptions(new OptionsResolver())))
            ->checkModelForm($form)
            ->save($model, $options['saveOptions'])
        ;

        return $model;
    }

    /**
     * Configures options for update data
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureUpdateDataOptions(OptionsResolver $resolver): OptionsResolver
    {
        $this->configureGetFormOptions($resolver);
        $this->configureProcessModelFormOptions($resolver);

        $resolver->setRequired('model');

        return $resolver;
    }

    /**
     * @param array $options
     * @return mixed
     */
    public function updateData(array $options)
    {
        $options = $this->configureUpdateDataOptions(new OptionsResolver())->resolve($options);

        $form = $this
            ->getForm((new OptionsFilter())->removeUndefinedOptions($options, $this->configureGetFormOptions(new OptionsResolver())))
        ;

        $this
            ->processModelForm($form, (new OptionsFilter())->removeUndefinedOptions($options, $this->configureProcessModelFormOptions(new OptionsResolver())))
            ->checkModelForm($form)
        ;

        return $options['model'];
    }

    /**
     * Returns create model form
     * @param array $options
     * @return FormInterface
     */
    public function getCreateModelForm(array $options = []): FormInterface
    {
        if (!isset($options['formClass']) || !$options['formClass']) {
            $options['formClass'] = $this->getCreateModelFormType();
        }

        return $this->getForm($options);
    }

    /**
     * Creates new item
     * @param array $options
     * @return SimpleModelInterface
     */
    public function createModel(array $options = []): SimpleModelInterface
    {
        if (!isset($options['formClass']) || !$options['formClass']) {
            $options['formClass'] = $this->getCreateModelFormType();
        }

        return $this->updateModelData($options);
    }

    /**
     * Returns form type class for updateModel
     * @return string
     */
    public function getUpdateModelFormType(): string
    {
        $formType = static::FORM_TYPE_UPDATE_ITEM ?: static::FORM_TYPE_CREATE_ITEM;
        if (!$formType) {
            throw new ManagerException(sprintf('Undefined constant FORM_TYPE_UPDATE_ITEM OR FORM_TYPE_CREATE_ITEM for %s.', get_class($this)));
        }

        return $formType;
    }

    /**
     * Returns create model form
     * @param array $options
     * @return FormInterface
     */
    public function getUpdateModelForm(array $options = []): FormInterface
    {
        if (!isset($options['formClass']) || !$options['formClass']) {
            $options['formClass'] = $this->getUpdateModelFormType();
        }

        return $this->getForm($options);
    }

    /**
     * Updates existing model
     * @param array $options
     * @return SimpleModelInterface
     */
    public function updateModel(array $options = []): SimpleModelInterface
    {
        if (!isset($options['formClass']) || !$options['formClass']) {
            $options['formClass'] = $this->getUpdateModelFormType();
        }

        return $this->updateModelData($options);
    }

    /**
     * Updates existing model by id
     * @param int|string|null $id
     * @param array $options
     * @return SimpleModelInterface
     */
    public function updateModelById($id, array $options = []): SimpleModelInterface
    {
        return $this->updateModel(array_merge([
            'model' => $this->getItem($id),
        ], $options));
    }

    /**
     * Configures options for create from data
     * @param OptionsResolver $resolver
     * @return OptionsResolver
     */
    protected function configureProcessModelFormOptions(OptionsResolver $resolver): OptionsResolver
    {
        $resolver->setDefaults([
            'formName'      => null,
            'data'          => [],
            'directSubmit'  => false,
            'directSubmitClearMissing' => false,
            'request'       => null,
            'requestMethod' => Request::METHOD_POST,
        ]);

        $resolver->setRequired('formName');

        $resolver->setAllowedValues('requestMethod', [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH]);

        $resolver->setAllowedTypes('request',                   ['null', Request::class]);
        $resolver->setAllowedTypes('data',                      'array');
        $resolver->setAllowedTypes('directSubmit',              'bool');
        $resolver->setAllowedTypes('directSubmitClearMissing',  'bool');

//        $resolver->setNormalizer('request', function (Options $options, $request) {
//            if (!$request && !$options['directSubmit']) {
//                $request = new Request([], $options['formName'] ? [$options['formName'] => $options['data']] : $options['data']);
//                $request->setMethod($options['requestMethod']);
//            }
//            return $request;
//        });
        $resolver->setNormalizer('directSubmit', function (Options $options, $directSubmit) {
            if (!$directSubmit && $options['data']) {
                return true;
            }
            return $directSubmit;
        });

        return $resolver;
    }

    /**
     * Processes model form (submitting)
     * @param FormInterface $form
     * @param array $options
     * @return AbstractManager
     */
    public function processModelForm(FormInterface $form, array $options = []): self
    {
        $options = $this->configureProcessModelFormOptions(new OptionsResolver())->resolve($options + ['formName' => $form->getName()]);

        if ($options['directSubmit']) {
            $form->submit($options['data'], $options['directSubmitClearMissing']);
        } else if ($form->getConfig()->getRequestHandler() instanceof HttpFoundationRequestHandler || is_null($options['request'])) {
            $form->handleRequest($options['request']);
        } else {
            throw new ManagerException(sprintf('Unable to handle "request" option because request handler is not %s. Use "directSubmit" and "data" options instead.', HttpFoundationRequestHandler::class));
        }

        return $this;
    }

    /**
     * Checks if form is submitted and valid
     * @param FormInterface $form
     * @return AbstractManager
     */
    public function checkModelForm(FormInterface $form): self
    {
        if (!$form->isSubmitted()) {
            $form->addError(new FormError('Form is not submitted'));
            throw new FormErrorsException($form);
        }
        if (!$form->isValid()) {
            throw new FormErrorsException($form);
        }

        return $this;
    }
}