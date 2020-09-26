<?php

namespace App\Tests\Entity;

use App\Entity\UsersProfiles;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UsersProfilesTest
 * @package App\Tests\Entity
 */
class UsersProfilesTest extends KernelTestCase
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
     * @return UsersProfiles
     */
    public function getEntity(): UsersProfiles
    {
        $profile = (new UsersProfiles())
            ->setAvatarFile(null)
            ->setAvatarName('user_avatar.jpg')
            ->setAvatarSize(1234)
        ;

        $this->entityManager->persist($profile);

        return $profile;
    }

    /**
     * @return void
     */
    public function testValidEntity(): void
    {
        $profile = $this->getEntity();

        $this->assertHasErrors($profile);
        $this->assertNull($profile->getAvatarFile());
        $this->assertNotNull($profile->getAvatarName());
        $this->assertGreaterThan(0, $profile->getAvatarSize());
        $this->assertInstanceOf(DateTime::class, $profile->getCreatedAt());
    }

    /**
     * @return void
     */
    public function testSaveProfileSuccessfully(): void
    {
        $profile = $this->getEntity();

        $this->entityManager->persist($profile);
        $this->entityManager->flush();

        $this->assertNotNull($profile->getId());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
