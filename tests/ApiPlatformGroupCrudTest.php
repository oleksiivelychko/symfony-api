<?php

namespace App\Tests;

use App\Tests\Abstracts\ApiPlatformTestCase;
use App\Tests\Contracts\ApiGroupCrudTestInterface;
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    DecodingExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};

class ApiPlatformGroupCrudTest extends ApiPlatformTestCase implements ApiGroupCrudTestInterface
{
    protected string $apiEndpoint = '/api/groups';
    protected string $entityName = 'Group';

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
            'headers' => $this->requestHeaders(),
            'json' => [
                'name' => 'group',
                'users' => [1, 2],
            ],
        ]);

        static::$currentId = json_decode($response->getContent())->id;

        $this->assertSuccessfulJson([
            'id'    => static::$currentId,
            'name'  => 'group',
            'users' => [
                [
                    'id'    => 1,
                    'name'  => 'user-01',
                    'email' => 'user-01@email.com',
                ],
                [
                    'id'    => 2,
                    'name'  => 'user-02',
                    'email' => 'user-02@email.com',
                ]
            ],
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
        static::createClient()->request('GET', $this->apiEndpoint.'/'.static::$currentId, [
            'headers' => $this->requestHeaders(),
        ]);

        $this->assertSuccessfulJson([
            'id'    => static::$currentId,
            'name'  => 'group',
            'users' => [
                [
                    'id'    => 1,
                    'name'  => 'user-01',
                    'email' => 'user-01@email.com',
                ],
                [
                    'id'    => 2,
                    'name'  => 'user-02',
                    'email' => 'user-02@email.com',
                ]
            ],
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
        static::createClient()->request('PUT', $this->apiEndpoint.'/'.static::$currentId, [
            'headers' => $this->requestHeaders(),
            'json'=> [
                'name' => 'group-0',
                'users' => [2],
            ],
        ]);

        $this->assertSuccessfulJson([
            'id'    => static::$currentId,
            'name'  => 'group-0',
            'users' => [
                [
                    'id'    => 2,
                    'name'  => 'user-02',
                    'email' => 'user-02@email.com',
                ]
            ],
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
        static::createClient()->request('DELETE', $this->apiEndpoint.'/'.static::$currentId);
        $this->assertResponseIsSuccessful();
    }
}
