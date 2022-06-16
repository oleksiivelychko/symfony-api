<?php

namespace App\Tests\Abstracts;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    DecodingExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};

abstract class ApiPlatformTestCase extends ApiTestCase
{
    static protected int $currentId = 0;
    protected string $apiEndpoint = '/api';
    protected string $schema = 'json';
    protected string $entityName = '';

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function assertSuccessfulJson(array $assertData): void
    {
        $this->assertResponseIsSuccessful();

        if ($this->schema === 'ld+json') {
            $assertData['@context'] = '/api/contexts/'.$this->entityName;
            $assertData['@id']      = $this->apiEndpoint.'/'.static::$currentId;
            $assertData['@type']    = $this->entityName;
        }

        $this->assertJsonContains($assertData);
    }

    protected function requestHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/'.$this->schema,
        ];
    }
}