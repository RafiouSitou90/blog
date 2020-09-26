<?php

namespace App\Tests\Entity;

use App\Entity\Posts;
use App\Entity\PostsComments;
use App\Entity\Users;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PostsCommentsTest
 * @package App\Tests\Entity
 */
class PostsCommentsTest extends KernelTestCase
{
    use AssertionErrorsTraits;
    use FixturesTrait;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @var array
     */
    private array $dataUser = [];

    /**
     * @var array
     */
    private array $dataPost = [];

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$container->get('doctrine')->getManager(); /** @phpstan-ignore-line */

        $this->dataPost = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        $this->dataUser = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UsersFixturesTest.yaml'
        ]);

        parent::setUp();
    }

    /**
     * @return PostsComments
     */
    public function getEntity(): PostsComments
    {
        /** @var Posts $post */
        $post = $this->dataPost['post'];

        /** @var Users $user */
        $user = $this->dataUser['user'];

        $comment = (new PostsComments())
            ->setAuthor($user)
            ->setPost($post)
            ->setContent('New comment of the post')
            ->setState('submitted')
            ->setPublishedAt(null)
        ;

        $this->entityManager->persist($comment);

        return $comment;
    }

    /**
     * @return void
     */
    public function testValidEntity(): void
    {
        $comment = $this->getEntity();

        $this->assertHasErrors($comment);
        $this->assertEquals('New comment of the post', $comment->getContent());
        $this->assertEquals('submitted', $comment->getState());
        $this->assertNull($comment->getPublishedAt());
        $this->assertInstanceOf(DateTime::class, $comment->getCreatedAt());
        $this->assertInstanceOf(Posts::class, $comment->getPost());
        $this->assertInstanceOf(Users::class, $comment->getAuthor());
    }

    /**
     * @return void
     */
    public function testValidCommentWithReply(): void
    {
        $comment = $this->getEntity();

        $commentResponse = ($this->getEntity())->setContent("New comment's response for the post");
        $this->assertNotNull($comment->getId());
        $this->assertInstanceOf(PostsComments::class, $comment->addReply($commentResponse));
        $this->assertEquals(1, $comment->getReplies()->count());
        $this->assertNotNull($comment->getReplies()[0]);
        $this->assertInstanceOf(PostsComments::class, $comment->getReplies()[0]);

        $this->assertInstanceOf(PostsComments::class, $comment->removeReply($commentResponse));
        $this->assertEquals(0, $comment->getReplies()->count());
        $this->assertNull($comment->getReplies()[0]);
    }

    /**
     * @return void
     */
    public function testInvalidContent(): void
    {
        $comment = $this->getEntity();

        $this->assertHasErrors($comment->setContent('comment'), 1);
        $this->assertHasErrors($comment->setContent(''), 2);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
