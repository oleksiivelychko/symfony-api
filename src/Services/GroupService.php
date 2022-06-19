<?php

namespace App\Services;

use App\Dto\Http\GroupDTO;
use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class GroupService extends AbstractEntityService
{
    private GroupRepository $groupRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);

        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    public function list(): Group|array
    {
        /**
         * @var Group $group
         */
        foreach ($this->groupRepository->findWithUsers() as $group) {
            $data[] = $group->jsonSerialize();
        }

        return $data ?? [];
    }

    public function create(GroupDTO $request): Group
    {
        $group = new Group();
        $group->setName($request->getName());

        foreach ($request->getUsers() as $userId) {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $group->addUser($user);
            }
        }

        $this->persistAndFlush($group);

        return $group;
    }

    public function update(GroupDTO $request, int $id): ?Group
    {
        $group = $this->groupRepository->find($id);
        if (!$group) {
            return null;
        }

        $group->setName($request->getName());

        $userIds = $request->getUsers();
        if (!empty($userIds)) {
            $group->removeUsers();
            foreach ($userIds as $userId) {
                $user = $this->userRepository->find($userId);
                if ($user) {
                    $group->addUser($user);
                }
            }
        }

        $this->persistAndFlush($group);

        return $group;
    }

    public function delete(int $id): ?Group
    {
        $group = $this->groupRepository->find($id);
        if (!$group) {
            return null;
        }

        $this->removeAndFlush($group);

        return $group;
    }
}