<?php

namespace App\Tests\Entity;

use App\Entity\Posts;
use App\Entity\PostsComments;
use App\Entity\Ratings;
use App\Entity\Users;
use App\Entity\UsersProfiles;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UsersTest
 * @package App\Tests\Entity
 */
class UsersTest extends KernelTestCase
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

        $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UsersFixturesTest.yaml'
        ]);

        parent::setUp();
    }

    /**
     * @return Users
     */
    public function getEntity(): Users
    {
        $user = new Users();
        $user
            ->setFirstName('User First Name')
            ->setLastName('User Last Name')
            ->setUsername('User_Username')
            ->setEmail('user_email@domain.com')
            ->setProfile(null)
            ->setRoles(['ROLE_USER'])
            ->setPassword('user_password_not_hashed')
        ;
        $this->entityManager->persist($user);

        return $user;
    }

    /**
     * @return void
     */
    public function testValidUsersEntity(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user);
        $this->assertEquals('User First Name', $user->getFirstName());
        $this->assertEquals('User Last Name', $user->getLastName());
        $this->assertEquals('User First Name User Last Name', $user->getFullName());
        $this->assertEquals('User_Username', $user->getUsername());
        $this->assertEquals('user_email@domain.com', $user->getEmail());
        $this->assertEquals(null, $user->getProfile());
        $this->assertNotNull($user->getPassword());
        $this->assertInstanceOf(DateTime::class, $user->getCreatedAt());
    }

    /**
     * @return void
     */
    public function testInvalidUsername(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setUsername('u_name'), 1);
        $this->assertHasErrors($user->setUsername(''), 2);
    }

    /**
     * @return void
     */
    public function testInvalidEmail(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setEmail('email.domain.com'), 1);
        $this->assertHasErrors($user->setEmail(''), 1);
    }

    /**
     * @return void
     */
    public function testInvalidFirstName(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setFirstName('n'), 1);
        $this->assertHasErrors($user->setFirstName(''), 2);
    }

    /**
     * @return void
     */
    public function testInvalidLastName(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setLastName('n'), 1);
        $this->assertHasErrors($user->setLastName(''), 2);
    }

    /**
     * @return void
     */
    public function testEntityWithDuplicateUsername(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setUsername('username'), 1);
    }

    /**
     * @return void
     */
    public function testEntityWithDuplicateEmail(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setEmail('email@domain.com'), 1);
    }

    /**
     * @return void
     */
    public function testSaveUserSuccessfully(): void
    {
        $user = $this->saveUser();

        $this->assertNotNull($user->getId());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    /**
     * @return void
     */
    public function testUserEntityPost(): void
    {
        $user = $this->saveUser();
        $post = new Posts();

        $this->assertNull($user->getPosts()[0]);
        $this->assertInstanceOf(Users::class, $user->addPost($post));
        $this->assertInstanceOf(Posts::class, $user->addPost($post)->getPosts()[0]);
        $this->assertInstanceOf(Users::class, $user->removePost($post));
        $this->assertNull($user->removePost($post)->getPosts()[0]);
    }

    /**
     * @return void
     */
    public function testUserEntityComment(): void
    {
        $user = $this->saveUser();

        $comment = new PostsComments();

        $this->assertNull($user->getComments()[0]);
        $this->assertInstanceOf(Users::class, $user->addComment($comment));
        $this->assertInstanceOf(PostsComments::class, $user->addComment($comment)->getComments()[0]);
        $this->assertInstanceOf(Users::class, $user->removeComment($comment));
        $this->assertNull($user->removeComment($comment)->getComments()[0]);
    }

    /**
     * @return void
     */
    public function testUserEntityVote(): void
    {
        $user = $this->saveUser();

        $rating = new Ratings();

        $this->assertNull($user->getRatings()[0]);
        $this->assertInstanceOf(Users::class, $user->addRating($rating));
        $this->assertInstanceOf(Ratings::class, $user->addRating($rating)->getRatings()[0]);
        $this->assertInstanceOf(Users::class, $user->removeRating($rating));
        $this->assertNull($user->removeRating($rating)->getRatings()[0]);
    }

    /**
     * @return void
     */
    public function testUserEntityProfile(): void
    {
        $user = $this->saveUser();

        $profile = new UsersProfiles();

        $this->assertNull($user->getProfile());
        $this->assertInstanceOf(Users::class, $user->setProfile($profile));
        $this->assertInstanceOf(UsersProfiles::class, $user->setProfile($profile)->getProfile());
        $this->assertInstanceOf(Users::class, $user->setProfile(null));
        $this->assertNull($user->setProfile(null)->getProfile());
    }

    /**
     * @return Users
     */
    private function saveUser(): Users
    {
        $user = $this->getEntity();

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
