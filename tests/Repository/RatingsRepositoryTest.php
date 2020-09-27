<?php

namespace App\Tests\Repository;

use App\Repository\RatingsRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RatingsRepositoryTest
 * @package App\Tests\Repository
 */
class RatingsRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var array
     */
    protected array $ratingsData = [];

    /**
     * @var RatingsRepository|null
     */
    private ?RatingsRepository $ratingsRepository;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->ratingsData = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        /** @phpstan-ignore-next-line */
        $this->ratingsRepository = self::$container->get(RatingsRepository::class);

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $postsRatings = $this->ratingsRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(10, $postsRatings);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
