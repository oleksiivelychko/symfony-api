<?php

namespace App\Tests;

use App\Entity\Group;
use App\Entity\User;
use App\Services\UserService;
use App\Tests\Abstracts\ApiMockTestCase;
use App\Tests\Contracts\ApiUserCrudTestInterface;
use PHPUnit\Framework\MockObject\MockClass;

class ApiUserCrudMockTest extends ApiMockTestCase implements ApiUserCrudTestInterface
{
    private UserService|MockClass $userService;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->userService = new UserService(
            $this->entityManager,
            $this->groupRepository,
            $this->userRepository
        );
    }

    public function testListUsers(): void
    {
        $mockUser1 = $this->createMock(User::class);
        $mockUser1
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedUser1);

        $mockUser2 = $this->createMock(User::class);
        $mockUser2
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedUser2);

        $this->userRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$mockUser1, $mockUser2]);

        $list = $this->userService->list();

        $this->assertJsonStringEqualsJsonString(json_encode(
            [$this->expectedUser1, $this->expectedUser2]), json_encode($list)
        );
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function testCreateUser(): void
    {
        $mockGroup1 = $this->createMock(Group::class);
        $mockGroup1
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $mockGroup1
            ->expects($this->once())
            ->method('getName')
            ->willReturn('group-01');

        $mockGroup2 = $this->createMock(Group::class);
        $mockGroup2
            ->expects($this->once())
            ->method('getId')
            ->willReturn(2);
        $mockGroup2
            ->expects($this->once())
            ->method('getName')
            ->willReturn('group-02');

        $this->groupRepository
            ->expects($this->exactly(2))
            ->method('find')
            ->withConsecutive([1], [2])
            ->willReturn($mockGroup1, $mockGroup2);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function($entity) {
                if ($entity instanceof User) {
                    $entity->setId(1);
                }
            });

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $user = $this->userService->create((object)[
            'name'  => 'user-01',
            'email'  => 'user-01@email.com',
            'groups' => [1,2]
        ]);

        $this->assertJsonStringEqualsJsonString(json_encode($this->expectedUser1), json_encode($user));
    }

    public function testCreateNonUniqueUser(): void
    {
        $this->markTestSkipped('Will be skipped `testCreateNonUniqueUser` test case');
    }

    public function testGetUser(): void
    {
        $mockUser = $this->createMock(User::class);
        $mockUser
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedUser1);

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($mockUser);

        $user = $this->userService->get(1);

        $this->assertJsonStringEqualsJsonString(json_encode($this->expectedUser1), json_encode($user));
    }

    public function testUpdateUser(): void
    {
        $mockUser = $this->createMock(User::class);
        $mockUser
            ->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn($this->expectedUser2);

        $mockUser
            ->expects($this->once())
            ->method('setName');

        $mockUser
            ->expects($this->once())
            ->method('removeGroups');

        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(2)
            ->willReturn($mockUser);

        $mockGroup = $this->createMock(Group::class);
        $this->groupRepository
            ->expects($this->once())
            ->method('find')
            ->with(2)
            ->willReturn($mockGroup);

        $mockUser
            ->expects($this->once())
            ->method('addGroup')
            ->with($mockGroup)
            ->willReturn($mockUser);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $user = $this->userService->update((object)[
            'name'  => 'user-02',
            'groups' => [2]
        ], 2);

        $this->assertJsonStringEqualsJsonString(json_encode($this->expectedUser2), json_encode($user));
    }

    public function testDeleteUser(): void
    {
        $mockUser = $this->createMock(User::class);
        $this->userRepository
            ->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($mockUser);

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($mockUser)
            ->willReturn($mockUser);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $user = $this->userService->delete(1);

        $this->assertNotNull($user);
    }
}
