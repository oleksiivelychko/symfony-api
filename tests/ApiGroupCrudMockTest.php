<?php

namespace App\Tests;

use App\Entity\Group;
use App\Entity\User;
use App\Tests\Abstracts\ApiMockTestCase;
use App\Tests\Contracts\ApiGroupCrudTestInterface;

class ApiGroupCrudMockTest extends ApiMockTestCase implements ApiGroupCrudTestInterface
{
    public function testListGroups(): void
    {
        $mockGroup1 = $this->createMock(Group::class);
        $mockGroup1
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedGroup1);

        $mockGroup2 = $this->createMock(Group::class);
        $mockGroup2
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedGroup1);

        $this->groupRepository
            ->expects($this->once())
            ->method('findWithUsers')
            ->willReturn([$mockGroup1, $mockGroup2]);

        $list = $this->groupService->list();

        $this->assertJsonStringEqualsJsonString(json_encode(
            [$this->expectedGroup1, $this->expectedGroup1]), json_encode($list)
        );
    }

    public function testCreateGroup(): void
    {
        $mockUser1 = $this->createMock(User::class);
        $mockUser1
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $mockUser1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('user-01');
        $mockUser1
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn('user-01@email.com');

        $mockUser2 = $this->createMock(User::class);
        $mockUser2
            ->expects($this->once())
            ->method('getId')
            ->willReturn(2);
        $mockUser2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('user-02');
        $mockUser2
            ->expects($this->once())
            ->method('getEmail')
            ->willReturn('user-02@email.com');

        $this->userRepository
            ->expects($this->exactly(2))
            ->method('find')
            ->withConsecutive([1], [2])
            ->willReturn($mockUser1, $mockUser2);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function($entity) {
                if ($entity instanceof Group) {
                    $entity->setId(1);
                }
            });

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $group = $this->groupService->create((object)[
            'name'  => 'group-01',
            'users' => [1,2]
        ]);

        $this->assertJsonStringEqualsJsonString(json_encode($this->expectedGroup1), json_encode($group));
    }

    public function testGetGroup(): void
    {
        $mockGroup = $this->createMock(Group::class);
        $mockGroup
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedGroup1);

        $this->groupRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($mockGroup);

        $group = $this->groupService->get(1);

        $this->assertJsonStringEqualsJsonString(json_encode($this->expectedGroup1), json_encode($group));
    }

    public function testUpdateGroup(): void
    {
        $mockGroup = $this->createMock(Group::class);
        $mockGroup
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedGroup2);

        $mockGroup
            ->expects($this->once())
            ->method('setName');

        $mockGroup
            ->expects($this->once())
            ->method('removeUsers');

        $this->groupRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($mockGroup);

        $mockUser = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(2)
            ->willReturn($mockUser);

        $mockGroup
            ->expects($this->once())
            ->method('addUser')
            ->with($mockUser)
            ->willReturn($mockGroup);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $group = $this->groupService->update((object)[
            'name'  => 'group-02',
            'users' => [2]
        ], 1);

        $this->assertJsonStringEqualsJsonString(json_encode($this->expectedGroup2), json_encode($group));
    }

    public function testDeleteGroup(): void
    {
        $mockGroup = $this->createMock(Group::class);
        $this->groupRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($mockGroup);

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($mockGroup)
            ->willReturn($mockGroup);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $group = $this->groupService->delete(1);

        $this->assertNotNull($group);
    }
}
