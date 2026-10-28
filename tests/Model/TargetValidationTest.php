<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Model;

use Dmytrof\ModelsManagementBundle\Model\Target;
use Dmytrof\ModelsManagementBundle\Tests\Data\SomeModel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Validator\Validation;

class TargetValidationTest extends TestCase
{
    private function violatedProperties(Target $target): array
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();

        return array_map(fn ($violation) => $violation->getPropertyPath(), iterator_to_array($validator->validate($target)));
    }

    public function testEmptyTargetIsInvalid(): void
    {
        $this->assertEqualsCanonicalizing(['className', 'id'], $this->violatedProperties(new Target(new EventDispatcher())));
    }

    public function testTargetWithModelIsValid(): void
    {
        $model = (new SomeModel())->setId(1);

        $this->assertSame([], $this->violatedProperties(new Target(new EventDispatcher(), $model)));
    }
}
