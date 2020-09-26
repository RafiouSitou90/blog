<?php

namespace App\Tests\Entity;

use App\Entity\PostMedias;
use App\Entity\Posts;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PostMediasTest
 * @package App\Tests\Entity
 */
class PostMediasTest extends KernelTestCase
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

        $this->entityManager = self::$container->get('doctrine')->getManager(); /** @phpstan-ignore-line */

        parent::setUp();
    }

    /**
     * @return PostMedias
     */
    public function getEntity(): PostMedias
    {
        $data = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        /** @var Posts $post */
        $post = $data['post'];

        $postMedia = (new PostMedias())
            ->setMediaFile(null)
            ->setMediaName('test-post.jpg')
            ->setMediaSize(1230)
            ->setPost($post)
        ;

        $this->entityManager->persist($postMedia);

        return $postMedia;
    }

    /**
     * @return void
     */
    public function testValidEntity(): void
    {
        $postMedia = $this->getEntity();

        $this->assertHasErrors($postMedia);
        $this->assertNull($postMedia->getMediaFile());
        $this->assertEquals('test-post.jpg', $postMedia->getMediaName());
        $this->assertEquals(1230, $postMedia->getMediaSize());
        $this->assertInstanceOf(DateTime::class, $postMedia->getCreatedAt());
    }

    /**
     * @return void
     */
    public function testSavePostMediaSuccessfully(): void
    {
        $postMedia = $this->getEntity();

        $this->entityManager->flush();

        $this->assertNotNull($postMedia->getId());
        $this->assertInstanceOf(Posts::class, $postMedia->getPost());
    }
}
