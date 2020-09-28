<?php

namespace App\Tests\Entity;

use App\Entity\ResetPasswordRequest;
use App\Entity\Users;
use App\Tests\Traits\AssertionErrorsTraits;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ResetPasswordRequestTest
 * @package App\Tests\Entity
 */
class ResetPasswordRequestTest extends KernelTestCase
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

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testValidEntity(): void
    {
        $user = new Users();
        $expiresAt = new DateTime();
        $selector = 'selector';
        $hashedToken = 'hashed_token';
        $resetPasswordRequest = new ResetPasswordRequest($user, $expiresAt, $selector, $hashedToken);

        $this->assertIsObject($resetPasswordRequest->getUser());
    }
}
