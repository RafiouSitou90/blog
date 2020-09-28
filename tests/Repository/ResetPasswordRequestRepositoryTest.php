<?php

namespace App\Tests\Repository;

use App\Entity\Users;
use App\Repository\ResetPasswordRequestRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;

/**
 * Class ResetPasswordRequestRepositoryTest
 * @package App\Tests\Repository
 */
class ResetPasswordRequestRepositoryTest extends KernelTestCase
{
    /**
     * @var ResetPasswordRequestRepository|null
     */
    protected ?ResetPasswordRequestRepository $resetPasswordRequestRepository;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::bootKernel();

        /** @phpstan-ignore-next-line */
        $this->resetPasswordRequestRepository = self::$container->get(ResetPasswordRequestRepository::class);

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testCreateResetPasswordRequest(): void
    {
        $user = new Users();
        $expiresAt = new DateTime();
        $selector = 'selector';
        $hashedToken = 'hashed_token';

        /** @phpstan-ignore-next-line */
        $response = $this->resetPasswordRequestRepository->createResetPasswordRequest(
            $user,
            $expiresAt,
            $selector,
            $hashedToken
        );

        $this->assertInstanceOf(ResetPasswordRequestInterface::class, $response);
    }
}
