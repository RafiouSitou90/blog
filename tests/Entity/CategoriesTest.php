<?php

namespace App\Tests\Entity;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoriesTest
 * @package App\Tests\Entity
 */
class CategoriesTest extends KernelTestCase
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

        $this->entityManager = self::$container->get('doctrine')->getManager(); /** @phpstan-ignore-line */

        parent::setUp();
    }

    /**
     * Return a category entity with default data
     *
     * @return Categories
     */
    private function getEntity(): Categories
    {
        $category = (new Categories())->setName('Category name');

        $this->entityManager->persist($category);

        return $category;
    }

    /**
     * @return void
     */
    public function testValidEntity(): void
    {
        $category = $this->getEntity();

        $this->assertHasErrors($category);
        $this->assertNotNull($category->getName());
        $this->assertInstanceOf(DateTime::class, $category->getCreatedAt());
    }

    /**
     * @return void
     */
    public function testInvalidEntity(): void
    {
        $category = $this->getEntity();

        $this->assertHasErrors($category->setName('Ca'), 1);
        $this->assertHasErrors($category->setName(''), 2);
    }

    /**
     * @return void
     */
    public function testSaveCategorySuccessfully(): void
    {
        $category = $this->getEntity();

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->assertNotNull($category->getId());
        $this->assertEquals('category-name', $category->getSlug());
    }

    /**
     * @return void
     */
    public function testCategoryWithDuplicatesName(): void
    {
        $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/CategoriesFixturesTest.yaml'
        ]);

        $this->assertHasErrors($this->getEntity()->setName('First category'), 1);
    }

    /**
     * @return void
     */
    public function testEntityPost(): void
    {
        $category = $this->getEntity();
        $this->entityManager->flush();

        $post = (new Posts())->setCategory($category);

        $this->assertInstanceOf(Categories::class, $category->addPost($post));
        $this->assertInstanceOf(Posts::class, $category->addPost($post)->getPosts()[0]);
        $this->assertInstanceOf(Categories::class, $category->removePost($post));
        $this->assertNull($category->removePost($post)->getPosts()[0]);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testEntityTimestampable(): void
    {
        $category = $this->getEntity();
        $category->setCreatedAt(new DateTime())->setUpdatedAt(null);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->assertNull($category->getUpdatedAt());
        $this->assertInstanceOf(Categories::class, $category->setUpdatedAtValue());
        $this->assertInstanceOf(DateTime::class, $category->setUpdatedAtValue()->getUpdatedAt());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
