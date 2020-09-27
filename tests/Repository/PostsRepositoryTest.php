<?php

namespace App\Tests\Repository;

use App\Entity\Posts;
use App\Repository\PostsRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PostsRepositoryTest
 * @package App\Tests\Repository
 */
class PostsRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var array
     */
    protected array $postsData = [];

    /**
     * @var PostsRepository|null
     */
    private ?PostsRepository $postsRepository;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->postsData = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        $this->postsRepository = self::$container->get(PostsRepository::class); /** @phpstan-ignore-line */

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $postMedia = $this->postsRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(1, $postMedia);
    }

    /**
     * @return void
     */
    public function testFindBySlug(): void
    {
        /** @phpstan-ignore-next-line */
        $postMedia = $this->postsRepository->findOneBy([
            'slug' => 'title-of-the-test-post-test-new-post-category'
        ]);

        $this->assertNotNull($postMedia);
        $this->assertInstanceOf(Posts::class, $postMedia);
        $this->assertEquals('Title of the test post', $postMedia->getTitle());
    }



    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
