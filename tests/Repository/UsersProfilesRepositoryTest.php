<?php

namespace App\Tests\Repository;

use App\Repository\UsersProfilesRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UsersProfilesRepositoryTest
 * @package App\Tests\Repository
 */
class UsersProfilesRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var UsersProfilesRepository|null
     */
    protected ?UsersProfilesRepository $usersProfilesRepository;

    /**
     * @var array
     */
    protected array $usersData = [];

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->usersData = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UsersFixturesTest.yaml'
        ]);

        /** @phpstan-ignore-next-line */
        $this->usersProfilesRepository = self::$container->get(UsersProfilesRepository::class);

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $usersProfiles = $this->usersProfilesRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(13, $usersProfiles);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
