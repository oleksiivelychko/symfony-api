<?php

namespace App\Tests\Abstracts;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockClass;
use PHPUnit\Framework\TestCase;

abstract class ApiMockTestCase extends TestCase
{
    protected EntityManagerInterface|MockClass $entityManager;

    protected GroupRepository|MockClass $groupRepository;

    protected UserRepository|MockClass $userRepository;

    protected array $expectedGroup1 = [
        'id' => 1,
        'name' => 'group-01',
        'users' => [
            [
                'id' => 1,
                'name' => 'user-01',
                'email' => 'user-01@email.com',
            ],
            [
                'id' => 2,
                'name' => 'user-02',
                'email' => 'user-02@email.com',
            ]
        ],
    ];

    protected array $expectedGroup2 = [
        'id'    => 2,
        'name'  => 'group-02',
        'users' => [[
            'id' => 2,
            'name' => 'user-02',
            'email' => 'user-02@email.com',
        ]],
    ];

    protected array $expectedUser1 = [
        'id' => 1,
        'name' => 'user-01',
        'email' => 'user-01@email.com',
        'groups' => [
            [
                'id' => 1,
                'name' => 'group-01',
            ],
            [
                'id' => 2,
                'name' => 'group-02',
            ]
        ],
    ];

    protected array $expectedUser2 = [
        'id' => 2,
        'name' => 'user-02',
        'email' => 'user-02@email.com',
        'groups' => [
            [
                'id' => 2,
                'name' => 'group-02',
            ]
        ],
    ];

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->groupRepository = $this->createMock(GroupRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
    }
}