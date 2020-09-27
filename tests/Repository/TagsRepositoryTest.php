<?php

namespace App\Tests\Repository;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TagsRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    /**
     * @var TagsRepository|null
     */
    protected ?TagsRepository $tagsRepository;

    /**
     * @var array
     */
    protected array $tagsData = [];

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();
        $this->tagsData = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/TagsFixturesTest.yaml'
        ]);

        $this->tagsRepository = self::$container->get(TagsRepository::class); /** @phpstan-ignore-line */

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCountRows(): void
    {
        $tags = $this->tagsRepository->count([]); /** @phpstan-ignore-line */

        $this->assertEquals(10, $tags);
    }

    /**
     * @return void
     */
    public function testFindByName(): void
    {
        $tags = $this->tagsRepository->findOneBy(['name' => 'tag-name-1']); /** @phpstan-ignore-line */

        $this->assertNotNull($tags);
        $this->assertIsNotArray($tags);
        $this->assertInstanceOf(Tags::class, $tags);
        $this->assertSame('tag-name-1', $tags->getName());
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
