<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional\App\Manager;

use Dmytrof\ModelsManagementBundle\Manager\AbstractDoctrineManager;
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Entity\Comment;

class CommentManager extends AbstractDoctrineManager
{
    public const MODEL_CLASS = Comment::class;
}
