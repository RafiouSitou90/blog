<?php

namespace App\Tests\Repository;

use App\Repository\CategoriesRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class CategoriesRepositoryTest
 * @package App\Tests\Repository
 */
class CategoriesRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var CategoriesRepository|null
     */
    protected ?CategoriesRepository $categoriesRepository;

    /**
     * @var array
     */
    protected array $categoriesData = [];

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->categoriesData = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/CategoriesFixturesTest.yaml'
        ]);

        $this->categoriesRepository = self::$container->get(CategoriesRepository::class); /** @phpstan-ignore-line */

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $categories = $this->categoriesRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(3, $categories);
    }

    /**
     * @return void
     */
    public function testFindByName(): void
    {
        $category = $this->categoriesRepository->findOneBy(['name' => 'Third category']); /** @phpstan-ignore-line */

        $this->assertNotNull($category);
        $this->assertIsNotArray($category);
        $this->assertSame('Third category', $category->getName());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
