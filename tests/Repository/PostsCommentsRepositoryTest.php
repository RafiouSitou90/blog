<?php

namespace App\Tests\Repository;

use App\Repository\PostsCommentsRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PostsCommentsRepositoryTest
 * @package App\Tests\Repository
 */
class PostsCommentsRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var array
     */
    protected array $postMediaData = [];

    /**
     * @var PostsCommentsRepository|null
     */
    private ?PostsCommentsRepository $postsCommentsRepository;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->postMediaData = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        /** @phpstan-ignore-next-line */
        $this->postsCommentsRepository = self::$container->get(PostsCommentsRepository::class);

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $postsComment = $this->postsCommentsRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(10, $postsComment);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
