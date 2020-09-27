<?php

namespace App\Tests\Repository;

use App\Entity\Users;
use App\Repository\UsersRepository;
use Doctrine\ORM\NonUniqueResultException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UsersRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var UsersRepository|null
     */
    protected ?UsersRepository $usersRepository;

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

        $this->usersRepository = self::$container->get(UsersRepository::class); /** @phpstan-ignore-line */

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $users = $this->usersRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(13, $users);
    }


    /**
     * @return void
     */
    public function testFindByUsername(): void
    {
        $users = $this->usersRepository->findOneBy(['username' => 'admin_username']); /** @phpstan-ignore-line */

        $this->assertNotNull($users);
        $this->assertIsNotArray($users);
        $this->assertInstanceOf(Users::class, $users);
        $this->assertSame('admin_username', $users->getUsername());
    }

    /**
     * @return void
     */
    public function testFindByEmail(): void
    {
        $users = $this->usersRepository->findOneBy(['email' => 'email@domain.com']); /** @phpstan-ignore-line */

        $this->assertNotNull($users);
        $this->assertIsNotArray($users);
        $this->assertInstanceOf(Users::class, $users);
        $this->assertSame('email@domain.com', $users->getEmail());
    }

    /**
     * @return void
     * @throws NonUniqueResultException
     */
    public function testFindByUsernameOrEmail(): void
    {
        $users = $this->usersRepository->findUserByUsernameOrEmail('email@domain.com'); /** @phpstan-ignore-line */

        $this->assertNotNull($users);
        $this->assertIsNotArray($users);
        $this->assertInstanceOf(Users::class, $users);
        $this->assertSame('email@domain.com', $users->getEmail());

        $users = $this->usersRepository->findUserByUsernameOrEmail('username'); /** @phpstan-ignore-line */

        $this->assertNotNull($users);
        $this->assertIsNotArray($users);
        $this->assertInstanceOf(Users::class, $users);
        $this->assertSame('username', $users->getUsername());
    }

    /**
     * @return void
     */
    public function testUpgradePasswordSuccessfully(): void
    {
        $user = (new Users())
            ->setUsername('the_username')
            ->setEmail('the_email@domain.com')
            ->setFirstName('The First Name')
            ->setLastName('The Last Name')
        ;

        $passwordEncoded =
            '$argon2id$v=19$m=65536,t=4,p=1$GAUwK9RnkjJUwGyFO/F9uw$MmZm3/Hc6N2GbJ+pR+BsYG9Ro1QhGy7Fa24bFgc0euw';

        $this->usersRepository->upgradePassword($user, $passwordEncoded); /** @phpstan-ignore-line */

        $this->assertNotNull($user->getId());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
