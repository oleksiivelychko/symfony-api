<?php

namespace App\Tests\Contracts;

interface ApiUserCrudTestInterface
{
    public function testCreateUser(): void;

    public function testGetUser(): void;

    public function testUpdateUser(): void;

    public function testDeleteUser(): void;

    public function testCreateNonUniqueUser(): void;

    public function testListUsers(): void;
}