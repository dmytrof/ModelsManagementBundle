<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional\App\Entity;

use Dmytrof\ModelsManagementBundle\Model\{ConditionalDeletionInterface, EventableModelInterface, ModificationEventsInterface, SimpleModelInterface};
use Dmytrof\ModelsManagementBundle\Model\Traits\{EventableModelTrait, ModificationEventsTrait, SimpleModelTrait, TimestampableEntityTrait};
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Event\ArticlePublished;
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article implements SimpleModelInterface, ConditionalDeletionInterface, EventableModelInterface, ModificationEventsInterface
{
    use SimpleModelTrait, TimestampableEntityTrait, EventableModelTrait, ModificationEventsTrait;

    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    protected ?string $title = null;

    #[ORM\Column(type: 'boolean')]
    protected bool $locked = false;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function lock(): self
    {
        $this->locked = true;

        return $this;
    }

    public function publish(): self
    {
        return $this->addModificationEvent(new ArticlePublished($this));
    }

    public function canBeDeleted(): bool
    {
        return !$this->locked;
    }

    public function hasEventDispatcher(): bool
    {
        return isset($this->eventDispatcher);
    }
}
