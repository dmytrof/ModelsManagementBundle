<?php

/*
 * This file is part of the DmytrofModelsManagementBundle package.
 *
 * (c) Dmytro Feshchenko <dmytro.feshchenko@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dmytrof\ModelsManagementBundle\Tests\Manager;

use Dmytrof\ModelsManagementBundle\Manager\AbstractManager;
use Dmytrof\ModelsManagementBundle\Exception\{FormErrorsException,
    ManagerException,
    ModelValidationException,
    NotFoundException,
    NotRemovableModelException};
use Dmytrof\ModelsManagementBundle\Model\SimpleModelInterface;
use Dmytrof\ModelsManagementBundle\Tests\Data\{SomeModel, SomeModelManager};
use Symfony\Component\Form\{Extension\HttpFoundation\HttpFoundationExtension,
    FormError,
    FormFactoryBuilder,
    FormFactoryInterface,
    FormInterface};
use Symfony\Component\Validator\{ConstraintViolation,
    ConstraintViolationList,
    Validator\ValidatorInterface};
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AbstractManagerTest extends TestCase
{
    /**
     * @var AbstractManager
     */
    protected $manager;

    public function setUp(): void
    {
        parent::setUp();

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturnCallback(function (SomeModel $object) {
            if (!$object->getFoo()) {
                return new ConstraintViolationList([(new ConstraintViolation('Foo is blank', 'Foo is blank', [], null, 'foo', true))]);
            }
            return new ConstraintViolationList();
        });
        $formFactory = (new FormFactoryBuilder())->getFormFactory();

        $this->manager = new SomeModelManager($validator, $formFactory);
    }

    public function testValidatorAndFormFactory(): void
    {
        $this->assertInstanceOf(ValidatorInterface::class, $this->manager->getValidator());
        $this->assertInstanceOf(FormFactoryInterface::class, $this->manager->getFormFactory());
    }

    public function testGetModelClass(): void
    {
        $this->assertEquals(SomeModel::class, $this->manager->getModelClass());
    }

    public function testNew(): void
    {
        $this->assertEquals(new SomeModel(), $this->manager->new());
        $this->assertInstanceOf(SimpleModelInterface::class, $this->manager->new());
    }

    public function testGet(): void
    {
        $this->assertInstanceOf(SimpleModelInterface::class, $model = $this->manager->get(1));
        $this->assertInstanceOf(SomeModel::class, $model);
        $this->assertEquals(1, $model->getId());

        $this->assertNull($this->manager->get(2));
    }

    public function testGetItem(): void
    {
        $this->assertInstanceOf(SimpleModelInterface::class, $model = $this->manager->getItem(1));
        $this->assertInstanceOf(SomeModel::class, $model);
        $this->assertEquals(1, $model->getId());

        $this->expectException(NotFoundException::class);
        $this->manager->getItem(2);
    }

    public function testSave(): void
    {
        $item = new SomeModel();

        $this->assertSame($this->manager, $this->manager->save($item));

        $this->expectException(ModelValidationException::class);
        $this->manager->save($item, ['validate' => true]);

        $this->assertSame($this->manager, $this->manager->save($item->setFoo('qwe'), ['validate' => true]));
    }

    public function testRemove(): void
    {
        $item = new SomeModel();

        $this->expectException(NotRemovableModelException::class);
        $this->assertSame($this->manager, $this->manager->remove($item));

        $this->assertSame($this->manager, $this->manager->remove($item->setId(1)));
    }

    public function testRemoveById(): void
    {
        $this->assertSame($this->manager, $this->manager->removeById(1));

        $this->expectException(NotFoundException::class);
        $this->assertSame($this->manager, $this->manager->removeById(2));
    }

    public function testGetCreateAndUpdateModelForm(): void
    {
        $this->assertSame(SomeModelManager::FORM_TYPE_CREATE_ITEM, $this->manager->getCreateModelFormType());
        $this->assertInstanceOf($this->manager->getCreateModelFormType(), $this->manager->getCreateModelForm()->getConfig()->getType()->getInnerType());

        $this->assertSame(SomeModelManager::FORM_TYPE_CREATE_ITEM, $this->manager->getUpdateModelFormType());
        $this->assertInstanceOf($this->manager->getUpdateModelFormType(), $this->manager->getUpdateModelForm()->getConfig()->getType()->getInnerType());
    }

    public function testGetForm(): void
    {
        $this->assertInstanceOf(FormInterface::class, $form = $this->manager->getForm([
            'formClass' => $this->manager::FORM_TYPE_CREATE_ITEM,
        ]));

        $this->assertInstanceOf($this->manager::FORM_TYPE_CREATE_ITEM, $form->getConfig()->getType()->getInnerType());
        $this->assertEquals(Request::METHOD_POST, $form->getConfig()->getMethod());
        $this->assertEquals(Request::METHOD_POST, $form->getConfig()->getOption('method'));
        $this->assertFalse($form->getConfig()->getOption('allow_extra_fields'));
        $this->assertNull($form->getData());

        $model = (new SomeModel())->setId(2);
        $this->assertInstanceOf(FormInterface::class, $form = $this->manager->getForm([
            'formClass' => $this->manager::FORM_TYPE_CREATE_ITEM,
            'requestMethod' => Request::METHOD_PUT,
            'formOptions' => [
                'allow_extra_fields' => true,
            ],
            'model' => $model,
        ]));
        $this->assertEquals(Request::METHOD_PUT, $form->getConfig()->getMethod());
        $this->assertEquals(Request::METHOD_PUT, $form->getConfig()->getOption('method'));
        $this->assertTrue($form->getConfig()->getOption('allow_extra_fields'));
        $this->assertSame($model, $form->getData());
    }

//    public function testUpdateModelData(): void
//    {
//
//    }

    public function testProcessModelForm()
    {
        $form = $this->manager->getCreateModelForm();

        $this->assertSame($this->manager, $this->manager->processModelForm($form, [
            'data' => [
                'foo' => 'bar',
            ],
            'directSubmit' => true,
        ]));
        $this->assertEquals('bar', $form->getData()->getFoo());

        $this->expectException(ManagerException::class);
        $this->assertSame($this->manager, $this->manager->processModelForm($form, [
            'data' => [
                'foo' => 'bar',
            ],
        ]));

        $model = new SomeModel();
        $form = (new FormFactoryBuilder())->addExtension(new HttpFoundationExtension())->getFormFactory()->create($this->manager->getCreateModelFormType(), $model);
        $this->assertSame($this->manager, $this->manager->processModelForm($form, [
            'data' => [
                'foo' => 'bazz',
            ],
        ]));
        $this->assertEquals('bazz', $form->getData()->getFoo());
    }
}