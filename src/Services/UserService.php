<?php

namespace App\Services;

use App\Dto\Http\RequestDTOInterface;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class UserService extends AbstractEntityService
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

    public function list(): array
    {
        return $this->userRepository->findAll();
    }

    public function get(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function create(object $dto): ?User
    {
        $user = $this->userRepository->findOneByEmail($dto->email);
        if ($user) {
            return null;
        }

        $user = new User();
        $user->setName($dto->name);
        $user->setEmail($dto->email);

        if (!empty($dto->groups)) {
            foreach ($dto->groups as $groupId) {
                $group = $this->groupRepository->find($groupId);
                if ($group) {
                    $user->addGroup($group);
                }
            }
        }

        $this->persistAndFlush($user);

        return $user;
    }

    public function update(object $dto, int $id): ?User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return null;
        }

        $user->setName($dto->name);

        if (!empty($dto->groups)) {
            $user->removeGroups();
            foreach ($dto->groups as $groupId) {
                $group = $this->groupRepository->find($groupId);
                if ($group) {
                    $user->addGroup($group);
                }
            }
        }

        $this->entityManager->flush();
        return $user;
    }

    public function delete(int $id): ?User
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return null;
        }

        $this->removeAndFlush($user);
        return $user;
    }
}