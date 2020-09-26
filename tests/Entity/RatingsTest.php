<?php

namespace App\Tests\Entity;

use App\Entity\Posts;
use App\Entity\Ratings;
use App\Entity\Users;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RatingsTest
 * @package App\Tests\Entity
 */
class RatingsTest extends KernelTestCase
{
    use AssertionErrorsTraits;
    use FixturesTrait;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    /**
     * @var array
     */
    private array $dataUser = [];

    /**
     * @var array
     */
    private array $dataPost = [];

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$container->get('doctrine')->getManager(); /** @phpstan-ignore-line */

        $this->dataPost = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/PostsFixturesTest.yaml'
        ]);

        $this->dataUser = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UsersFixturesTest.yaml'
        ]);

        parent::setUp();
    }

    /**
     * @return Ratings
     */
    public function getEntity(): Ratings
    {
        /** @var Posts $post */
        $post = $this->dataPost['post'];

        /** @var Users $user */
        $user = $this->dataUser['user'];

        $rating = (new Ratings())
            ->setPost($post)
            ->setAuthor($user)
            ->setRating(1)
        ;

        $this->entityManager->persist($rating);

        return $rating;
    }

    /**
     * @return void
     */
    public function testValidEntity(): void
    {
        $rating = $this->getEntity();

        $this->assertHasErrors($rating);
        $this->assertInstanceOf(Users::class, $rating->getAuthor());
        $this->assertInstanceOf(Posts::class, $rating->getPost());
        $this->assertInstanceOf(DateTime::class, $rating->getCreatedAt());
        $this->assertEquals(1, $rating->getRating());
    }
}
