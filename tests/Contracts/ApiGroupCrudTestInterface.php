<?php

namespace App\Tests\Contracts;

interface ApiGroupCrudTestInterface
{
    public function testCreateGroup(): void;

    public function testGetGroup(): void;

    public function testUpdateGroup(): void;

    public function testDeleteGroup(): void;

    public function testListGroups(): void;
}