<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiPlatformUserCrudTest extends ApiTestCase
{
    static int $currentUserId = 0;

    private string $apiEndpoint = '/api/users';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateUser(): void
    {
        $response = static::createClient()->request('POST', $this->apiEndpoint, [
            'json' => [
                'name' => 'user-0',
                'email' => 'user-0@email.com',
            ]
        ]);

        static::$currentUserId = json_decode($response->getContent())->id;

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $this->apiEndpoint.'/'.static::$currentUserId,
            'name' => 'user-0',
            'email' => 'user-0@email.com',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCreateNonUniqueUser(): void
    {
        static::createClient()->request('POST', $this->apiEndpoint, [
            'json' => [
                'name' => 'user',
                'email' => 'user-0@email.com',
            ]
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetUser(): void
    {
        static::createClient()->request('GET', $this->apiEndpoint.'/'.static::$currentUserId);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $this->apiEndpoint.'/'.static::$currentUserId,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateUser(): void
    {
        static::createClient()->request('PUT', $this->apiEndpoint.'/'.static::$currentUserId, [
            'json' => [
                'name' => 'user-00',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $this->apiEndpoint.'/'.static::$currentUserId,
            'name' => 'user-00',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testAddToGroup()
    {
        static::createClient()->request('PUT', $this->apiEndpoint.'/'.static::$currentUserId, [
            'json' => [
                'groups' => [
                    '/api/groups/1',
                    '/api/groups/2',
                ]
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $this->apiEndpoint.'/'.static::$currentUserId,
            'groups' => [
                '/api/groups/1',
                '/api/groups/2',
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testDeleteUser(): void
    {
        static::createClient()->request('DELETE', $this->apiEndpoint.'/'.static::$currentUserId);
        $this->assertResponseIsSuccessful();
    }
}
