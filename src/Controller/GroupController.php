<?php

namespace App\Controller;

use App\Dto\Http\GroupDTO;
use App\Entity\Group;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api-v2/', name: 'group-api')]
final class GroupController extends RestfulController
{
    #[Route('groups', name: '_list-groups', methods: ['GET'])]
    public function listGroups(GroupRepository $groupRepository): JsonResponse
    {
        /**
         * @var Group $group
         */
        foreach ($groupRepository->findWithUsers() as $group) {
            $data[] = $group->jsonSerialize();
        }

        return $this->json($data ?? []);
    }

    #[Route('groups/{id}', name: '_get-group', methods: ['GET'])]
    public function getGroup(GroupRepository $groupRepository, int $id): JsonResponse
    {
        $group = $groupRepository->find($id);
        if (!$group) {
            return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json($group);
    }

    #[Route('groups', name: '_create-group', methods: ['POST'])]
    public function createGroup(
        GroupDTO $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
    ): JsonResponse
    {
        try {
            $group = new Group();
            $group->setName($request->getName());

            foreach ($request->getUsers() as $userId) {
                $user = $userRepository->find($userId);
                if ($user) {
                    $group->addUser($user);
                }
            }

            $entityManager->persist($group);
            $entityManager->flush();

            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_CREATED,
                'data'      => $group,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $this->unprocessableExceptionMessage($e)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('groups/{id}', name: '_update-group', methods: ['PUT'])]
    public function updateGroup(
        GroupDTO $request,
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        UserRepository $userRepository,
        int $id
    ): JsonResponse
    {
        try {
            $group = $groupRepository->find($id);
            if (!$group) {
                return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }

            $group->setName($request->getName());

            $userIds = $request->getUsers();
            if (!empty($userIds)) {
                $group->removeUsers();
                foreach ($userIds as $userId) {
                    $user = $userRepository->find($userId);
                    if ($user) {
                        $group->addUser($user);
                    }
                }
            }

            $entityManager->persist($group);
            $entityManager->flush();

            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_UPDATED,
                'data'      => $group,
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $this->unprocessableExceptionMessage($e)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('groups/{id}', name: '_delete-group', methods: ['DELETE'])]
    public function deleteGroup(
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        int $id)
    : JsonResponse
    {
        $group = $groupRepository->find($id);
        if (!$group) {
            return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($group);
        $entityManager->flush();

        return $this->json(['message' => self::ENTITY_HAS_BEEN_DELETED]);
    }
}