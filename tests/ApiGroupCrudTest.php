<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Controller\RestfulController;
use App\Tests\Contracts\ApiGroupCrudTestInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiGroupCrudTest extends ApiTestCase implements ApiGroupCrudTestInterface
{
    static int $currentGroupId = 0;

    private string $apiEndpoint = '/api-v2/groups';

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
                'name' => 'group',
            ]
        ]);

        static::$currentGroupId = json_decode($response->getContent())->id;

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'id'        => static::$currentGroupId,
            'name'      => $response->toArray()['name'] ?? null,
            'message'   => RestfulController::ENTITY_HAS_BEEN_CREATED,
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
            'id'    => static::$currentGroupId,
            'name'  => 'group',
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
                'name' => 'group-0',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'id'    => static::$currentGroupId,
            'name' => 'group-0',
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
