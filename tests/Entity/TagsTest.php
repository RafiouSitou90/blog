<?php

namespace App\Tests\Entity;

use App\Entity\Tags;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TagsTest
 * @package App\Tests\Entity
 */
class TagsTest extends KernelTestCase
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
     * @return Tags
     */
    public function getEntity(): Tags
    {
        $tag = (new Tags())->setName('TagName');

        $this->entityManager->persist($tag);

        return $tag;
    }

    /**
     * @return void
     */
    public function testValidEntity(): void
    {
        $tag = $this->getEntity();

        $this->assertHasErrors($tag);
        $this->assertEquals('TagName', $tag->getName());
        $this->assertInstanceOf(DateTime::class, $tag->getCreatedAt());
    }

    /**
     * @return void
     */
    public function testInvalidName(): void
    {
        $tag = $this->getEntity();

        $this->assertHasErrors($tag->setName('tag'), 1);
        $this->assertHasErrors($tag->setName(''), 2);
    }

    /**
     * @return void
     */
    public function testSaveTagSuccessfully(): void
    {
        $tag = $this->getEntity();

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        $this->assertNotNull($tag->getId());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
