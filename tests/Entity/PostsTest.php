<?php

namespace App\Tests\Entity;

use App\Entity\Categories;
use App\Entity\PostMedias;
use App\Entity\Posts;
use App\Entity\PostsComments;
use App\Entity\Ratings;
use App\Entity\Tags;
use App\Entity\Users;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PostsTest
 * @package App\Tests\Entity
 */
class PostsTest extends KernelTestCase
{
    use AssertionErrorsTraits;
    use FixturesTrait;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$container->get('doctrine')->getManager(); /**  @phpstan-ignore-line */
        parent::setUp();
    }

    /**
     * @return Posts
     */
    public function getEntity(): Posts
    {
        $data = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UsersFixturesTest.yaml',
            dirname(__DIR__). '/DataFixtures/CategoriesFixturesTest.yaml',
        ]);

        $post = (new Posts())
            ->setTitle('New post title')
            ->setSummary('New post summary')
            ->setContent('New post content')
            ->setCategory($data['category'])
            ->setAuthor($data['user'])
            ->setState(Posts::getDraft())
            ->setCommentState(Posts::getCommentOpened())
            ->setPublishedAt(null)
        ;

        $this->entityManager->persist($post);

        return $post;
    }

    /**
     * @return void
     */
    public function testValidPostEntity(): void
    {
        $post = $this->getEntity();

        $this->assertHasErrors($post);
        $this->assertEquals('New post title', $post->getTitle());
        $this->assertEquals('New post summary', $post->getSummary());
        $this->assertEquals('New post content', $post->getContent());
        $this->assertEquals(Posts::getCommentOpened(), $post->getCommentState());
        $this->assertEquals(Posts::getDraft(), $post->getState());
        $this->assertNull($post->getPublishedAt());
        $this->assertNull($post->getMedias()[0]);
        $this->assertInstanceOf(DateTime::class, $post->getCreatedAt());
        $this->assertInstanceOf(Users::class, $post->getAuthor());
        $this->assertInstanceOf(Categories::class, $post->getCategory());
    }

    /**
     * @return void
     */
    public function testInvalidTitle(): void
    {
        $post = $this->getEntity();

        $this->assertHasErrors($post->setTitle('title'), 1);
        $this->assertHasErrors($post->setTitle(''), 2);
    }

    /**
     * @return void
     */
    public function testInvalidSummary(): void
    {
        $post = $this->getEntity();

        $this->assertHasErrors($post->setSummary('summary'), 1);
        $this->assertHasErrors($post->setSummary(''), 2);
    }

    /**
     * @return void
     */
    public function testInvalidContent(): void
    {
        $post = $this->getEntity();

        $this->assertHasErrors($post->setContent('content'), 1);
        $this->assertHasErrors($post->setContent(''), 2);
    }

    /**
     * @return void
     */
    public function testSavePostSuccessfully(): void
    {
        $post = $this->getEntity();

        $this->entityManager->flush();

        $this->assertNotNull($post->getId());
        $this->assertEquals('new-post-title-first-category', $post->getSlug());
        $this->assertNotNull($post->getCreatedAt());
    }

    /**
     * @return void
     */
    public function testChangePostStatus(): void
    {
        $post = $this->getEntity();
        $post->setPublishedAt(new DateTime());

        $this->assertEquals(Posts::getDraft(), $post->getState());
        $this->assertEquals(Posts::getPublished(), $post->setState(Posts::getPublished())->getState());
        $this->assertNotNull($post->getPublishedAt());
        $this->assertInstanceOf(DateTime::class, $post->getPublishedAt());
    }

    /**
     * @return void
     */
    public function testChangePostCommentStatus(): void
    {
        $post = $this->getEntity();

        $this->assertEquals(
            Posts::getCommentClosed(),
            $post->setCommentState(Posts::getCommentClosed())->getCommentState()
        );
        $this->assertEquals(
            Posts::getCommentOpened(),
            $post->setCommentState(Posts::getCommentOpened())->getCommentState()
        );
    }

    /**
     * @return void
     */
    public function testEntityComment(): void
    {
        $post = $this->getEntity();

        $comment = new PostsComments();

        $this->assertNull($post->getComments()[0]);
        $this->assertInstanceOf(Posts::class, $post->addComment($comment));
        $this->assertInstanceOf(PostsComments::class, $post->addComment($comment)->getComments()[0]);
        $this->assertInstanceOf(Posts::class, $post->removeComment($comment));
        $this->assertNull($post->removeComment($comment)->getComments()[0]);
    }

    /**
     * @return void
     */
    public function testEntityTag(): void
    {
        $post = $this->getEntity();

        $tag = new Tags();

        $this->assertNull($post->getTags()[0]);
        $this->assertInstanceOf(Posts::class, $post->addTag($tag));
        $this->assertInstanceOf(Tags::class, $post->addTag($tag)->getTags()[0]);
        $this->assertInstanceOf(Posts::class, $post->removeTag($tag));
        $this->assertNull($post->removeTag($tag)->getTags()[0]);
    }

    /**
     * @return void
     */
    public function testEntityVote(): void
    {
        $post = $this->getEntity();

        $rating = new Ratings();

        $this->assertNull($post->getRatings()[0]);
        $this->assertInstanceOf(Posts::class, $post->addRating($rating));
        $this->assertInstanceOf(Ratings::class, $post->addRating($rating)->getRatings()[0]);
        $this->assertInstanceOf(Posts::class, $post->removeRating($rating));
        $this->assertNull($post->removeRating($rating)->getRatings()[0]);
    }

    /**
     * @return void
     */
    public function testEntityMedia(): void
    {
        $post = $this->getEntity();

        $postMedia = new PostMedias();

        $this->assertNull($post->getMedias()[0]);
        $this->assertInstanceOf(Posts::class, $post->addMedia($postMedia));
        $this->assertInstanceOf(PostMedias::class, $post->addMedia($postMedia)->getMedias()[0]);
        $this->assertInstanceOf(Posts::class, $post->removeMedia($postMedia));
        $this->assertNull($post->removeMedia($postMedia)->getMedias()[0]);
    }

    /**
     * @return void
     */
    public function testEntityWithDuplicateSlug(): void
    {
        $data = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        /** @var Categories $category */
        $category = $data['post_category'];

        /** @var Users $user */
        $user = $data['post_user'];

        $newPost = (new Posts())
            ->setTitle('Title of the test post')
            ->setSummary('Summary of the test post')
            ->setContent('Content of the test post')
            ->setCategory($category)
            ->setAuthor($user)
            ->setState(Posts::getDraft())
            ->setCommentState(Posts::getCommentOpened())
            ->setPublishedAt(null)
        ;

        $this->entityManager->persist($newPost);

        $this->assertHasErrors($newPost, 1);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
