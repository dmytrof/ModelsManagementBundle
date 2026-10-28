<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional\App\Repository;

use Dmytrof\ModelsManagementBundle\Repository\{EntityRepositoryInterface, Traits\EntityRepositoryTrait};
use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository implements EntityRepositoryInterface
{
    use EntityRepositoryTrait;
}
