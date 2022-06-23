<?php

namespace App\Services;

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
    ) {
        parent::__construct($entityManager);

        $this->groupRepository = $groupRepository;
        $this->userRepository = $userRepository;
    }

    public function list(): array
    {
        /**
         * @var Group $group
         */
        foreach ($this->groupRepository->findWithUsers() as $group) {
            $data[] = $group->jsonSerialize();
        }

        return $data ?? [];
    }

    public function get(int $id): ?Group
    {
        return $this->groupRepository->find($id);
    }

    public function create(object $dto): Group
    {
        $group = new Group();
        $group->setName($dto->name);

        foreach ($dto->users as $userId) {
            $user = $this->userRepository->find($userId);
            if ($user) {
                $group->addUser($user);
            }
        }

        $this->persist($group, true);

        return $group;
    }

    public function update(object $dto, int $id): ?Group
    {
        $group = $this->groupRepository->find($id);
        if (!$group) {
            return null;
        }

        $group->setName($dto->name);

        if (!empty($dto->users)) {
            $group->removeUsers();
            foreach ($dto->users as $userId) {
                $user = $this->userRepository->find($userId);
                if ($user) {
                    $group->addUser($user);
                }
            }
        }

        $this->getEntityManager()->flush();

        return $group;
    }

    public function delete(int $id): ?Group
    {
        $group = $this->groupRepository->find($id);
        if (!$group) {
            return null;
        }

        $this->remove($group, true);

        return $group;
    }
}