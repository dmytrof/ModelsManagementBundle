<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional\App\Manager;

use Dmytrof\ModelsManagementBundle\Manager\AbstractDoctrineManager;
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Entity\Article;

class ArticleManager extends AbstractDoctrineManager
{
    public const MODEL_CLASS = Article::class;
}
