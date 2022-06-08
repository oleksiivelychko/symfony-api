<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiPlatformGroupCrudTest extends ApiTestCase
{
    static int $currentGroupId = 0;

    private string $apiEndpoint = '/api/groups';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testCreateGroup(): void
    {
        $response = static::createClient()->request('POST', $this->apiEndpoint, [
            'json' => [
                'name' => 'group-01',
            ]
        ]);

        static::$currentGroupId = json_decode($response->getContent())->id;

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $this->apiEndpoint.'/'.static::$currentGroupId,
            'name' => 'group-01',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetGroup(): void
    {
        static::createClient()->request('GET', $this->apiEndpoint.'/'.static::$currentGroupId);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $this->apiEndpoint.'/'.static::$currentGroupId,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testUpdateGroup(): void
    {
        static::createClient()->request('PUT', $this->apiEndpoint.'/'.static::$currentGroupId, [
            'json' => [
                'name' => 'group-02',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $this->apiEndpoint.'/'.static::$currentGroupId,
            'name' => 'group-02',
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testDeleteGroup(): void
    {
        static::createClient()->request('DELETE', $this->apiEndpoint.'/'.static::$currentGroupId);
        $this->assertResponseIsSuccessful();
    }
}
