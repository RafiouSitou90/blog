<?php

namespace App\Tests\Controller;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    /**
     * @var KernelBrowser
     */
    protected KernelBrowser $client;

    /**
     * @var array
     */
    protected array $users = [];

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->users = $this->loadFixtureFiles([
            dirname(__DIR__). '/DataFixtures/UsersFixturesTest.yaml'
        ]);

        parent::setUp();
    }

    /**
     * @return void
     */
    public function testLoginPage(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertPageTitleContains('Log in!');
        $this->assertSelectorTextContains('h1', 'Sign in');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    /**
     * @return void
     */
    public function testLoginWithBadCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Sign in');
        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'fake_email@domain.com',
            'password' => 'fake_password'
        ]);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('h1', 'Sign in');
    }

    /**
     * @return void
     */
    public function testLoginSuccessfully(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Sign in')->form([
            'username' => 'user@domain.com',
            'password' => '123456789'
        ]);
        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('');
        $this->assertResponseHasHeader('Location', '/');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return void
     */
    public function testLoginSuccessfullyWithBadCsrfToken(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $csrfToken = 'fake_csrf_token';

        $this->client->request('POST', '/login', [
            'username' => 'user@domain.com',
            'password' => '123456789',
            '_csrf_token' => $csrfToken
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('h1', 'Sign in');
    }

    /**
     * @return void
     */
    public function testLoginSuccessfullyWithValidCsrfToken(): void
    {
        $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $tokenManager = static::$container->get(CsrfTokenManagerInterface::class);
        $csrfToken = $tokenManager->getToken('authenticate'); /** @phpstan-ignore-line */

        $this->client->request('POST', '/login', [
            'username' => 'user@domain.com',
            'password' => '123456789',
            '_csrf_token' => $csrfToken
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('');
        $this->assertResponseHasHeader('Location', '/');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return void
     */
    public function testLogout(): void
    {
        $this->client->request('GET', '/logout');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return void
     */
    public function testRedirectUserLoggedToHomepage(): void
    {
        $this->client->loginUser($this->users['user']);

        $this->client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->assertResponseRedirects('');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }
}
