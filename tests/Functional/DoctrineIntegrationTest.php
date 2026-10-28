<?php

namespace Dmytrof\ModelsManagementBundle\Tests\Functional;

use Dmytrof\ModelsManagementBundle\Exception\NotDeletableModelException;
use Dmytrof\ModelsManagementBundle\Model\DoctrinePaginator;
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Entity\{Article, Comment};
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Event\ArticlePublished;
use Dmytrof\ModelsManagementBundle\Tests\Functional\App\Manager\{ArticleManager, CommentManager};
use Dmytrof\ModelsManagementBundle\EventSubscriber\{ModelDoctrineSubscriber, ModificationEventsDoctrineSubscriber};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Runs the bundle against a real Symfony kernel, DoctrineBundle and an in-memory SQLite database.
 */
class DoctrineIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ArticleManager $articles;
    private CommentManager $comments;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->articles = $container->get(ArticleManager::class);
        $this->comments = $container->get(CommentManager::class);

        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        (new SchemaTool($this->em))->createSchema($metadata);
    }

    private function createArticle(string $title): Article
    {
        $article = $this->articles->new()->setTitle($title);
        $this->articles->save($article);

        return $article;
    }

    /**
     * @return string[] classes of the Doctrine listeners registered for the event
     */
    private function listenerClasses(string $event): array
    {
        $eventManager = $this->em->getEventManager();
        // getListeners() of Symfony's ContainerAwareEventManager fails for events without listeners
        if (!$eventManager->hasListeners($event)) {
            return [];
        }

        return array_map(get_class(...), array_values($eventManager->getListeners($event)));
    }

    public function testModelDoctrineSubscriberIsRegisteredAsDoctrineListener(): void
    {
        foreach ([Events::postLoad, Events::prePersist, Events::postPersist, Events::postUpdate, Events::preRemove, Events::postFlush] as $event) {
            $listeners = $this->listenerClasses($event);
            $this->assertSame(1, count(array_keys($listeners, ModelDoctrineSubscriber::class)), "ModelDoctrineSubscriber is registered once for $event");
        }
        foreach ([Events::postPersist, Events::postUpdate, Events::postRemove, Events::postFlush] as $event) {
            $listeners = $this->listenerClasses($event);
            $this->assertSame(1, count(array_keys($listeners, ModificationEventsDoctrineSubscriber::class)), "ModificationEventsDoctrineSubscriber is registered once for $event");
        }
    }

    public function testTimestampsAreSetByGedmoFromAttributes(): void
    {
        $this->assertFalse(static::getContainer()->has('annotation_reader'), 'doctrine/annotations is not needed');

        $article = $this->createArticle('First');
        $this->assertInstanceOf(\DateTimeInterface::class, $article->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $article->getUpdatedAt());

        $columns = $this->em->getConnection()->createSchemaManager()->listTableColumns('article');
        $this->assertArrayHasKey('created_at', $columns);
        $this->assertArrayHasKey('updated_at', $columns);
    }

    public function testCloneResetsTimestamps(): void
    {
        $clone = clone $this->createArticle('First');

        $this->assertNull($clone->getCreatedAt());
        $this->assertNull($clone->getUpdatedAt());
    }

    public function testNewModelGetsEventDispatcherViaPostLoad(): void
    {
        /** @var Article $article */
        $article = $this->articles->new();
        $this->assertTrue($article->hasEventDispatcher());


        $id = $this->createArticle('First')->getId();
        $this->em->clear();
        $this->assertTrue($this->articles->getItem($id)->hasEventDispatcher(), 'postLoad listener injects the dispatcher');
    }

    public function testPaginator(): void
    {
        foreach (range(1, 7) as $i) {
            $this->createArticle("Article $i");
        }

        $paginator = $this->articles->getDoctrinePaginator(['page' => 2, 'limit' => 3]);

        $this->assertInstanceOf(DoctrinePaginator::class, $paginator);
        $this->assertSame(2, $paginator->getPage());
        $this->assertSame(3, $paginator->getLimit());
        $this->assertSame(7, $paginator->getTotalCount());
        $this->assertSame(['Article 4', 'Article 5', 'Article 6'], array_map(fn (Article $a) => $a->getTitle(), $paginator->getItems()));
    }

    public function testIteratorYieldsEntities(): void
    {
        foreach (range(1, 5) as $i) {
            $this->createArticle("Article $i");
        }

        $titles = [];
        foreach ($this->articles->getIterator(['page' => 1, 'limit' => 3]) as $article) {
            $this->assertInstanceOf(Article::class, $article);
            $titles[] = $article->getTitle();
        }

        $this->assertSame(['Article 1', 'Article 2', 'Article 3'], $titles);
    }

    public function testRemove(): void
    {
        $id = $this->createArticle('Removable')->getId();

        $this->articles->removeById($id);

        $this->assertNull($this->articles->get($id));
    }

    public function testNotDeletableModelIsProtected(): void
    {
        $article = $this->createArticle('Locked')->lock();
        $this->articles->save($article);

        $this->expectException(NotDeletableModelException::class);
        $this->articles->remove($article);
    }

    public function testModificationEventsAreDispatchedAfterFlush(): void
    {
        $published = [];
        static::getContainer()->get(EventDispatcherInterface::class)
            ->addListener(ArticlePublished::class, function (ArticlePublished $event) use (&$published) {
                $published[] = $event->article->getTitle();
            });

        $article = $this->articles->new()->setTitle('News')->publish();
        $this->assertSame([], $published);

        $this->articles->save($article);

        $this->assertSame(['News'], $published);
        $this->assertSame([], $article->getModificationEvents());
    }

    public function testTargetIsRefreshedAfterFlushAndLoadedLazily(): void
    {
        $article = $this->articles->new()->setTitle('Commented');
        $comment = $this->comments->new()->setText('Nice!')->setTarget($article);

        // Both are new: the target id becomes known only after flush
        $this->em->persist($article);
        $this->comments->save($comment);

        $this->assertSame(['className' => Article::class, 'id' => $article->getId()], $comment->getTargetData());

        $commentId = $comment->getId();
        $this->em->clear();

        $loaded = $this->comments->getItem($commentId);
        $this->assertTrue($loaded->hasTarget());
        $this->assertSame('Commented', $loaded->getTarget()->getModel()->getTitle());
    }

    public function testValidationUsesAttributes(): void
    {
        $this->expectException(\Dmytrof\ModelsManagementBundle\Exception\ModelValidationException::class);

        $this->articles->save($this->articles->new(), ['validate' => true]);
    }
}
