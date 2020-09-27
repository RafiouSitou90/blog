<?php

namespace App\Tests\Repository;

use App\Repository\PostMediasRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PostMediasRepositoryTest
 * @package App\Tests\Repository
 */
class PostMediasRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var array
     */
    protected array $postMediaData = [];

    /**
     * @var PostMediasRepository|null
     */
    private ?PostMediasRepository $postMediaRepository;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->postMediaData = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        $this->postMediaRepository = self::$container->get(PostMediasRepository::class); /** @phpstan-ignore-line */

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $postMedia = $this->postMediaRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(5, $postMedia);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
