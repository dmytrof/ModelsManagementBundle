<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional\App\Event;

use Dmytrof\ModelsManagementBundle\Event\ModificationEvent;
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Entity\Article;

class ArticlePublished extends ModificationEvent
{
    public function __construct(public readonly Article $article)
    {
    }
}
