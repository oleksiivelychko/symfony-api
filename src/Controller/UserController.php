<?php

namespace App\Controller;

use App\Dto\Http\UserDTO;
use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api-v2/', name: 'user-api')]
final class UserController extends RestfulController
{
    #[Route('users', name: '_list-users', methods: ['GET'])]
    public function listUsers(UserRepository $userRepository): JsonResponse
    {
        $data = $userRepository->findAll();
        return $this->json($data);
    }

    #[Route('users/{id}', name: '_fetch-user', methods: ['GET'])]
    public function fetchUser(UserRepository $userRepository, int $id): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user);
    }

    #[Route('users', name: '_create-user', methods: ['POST'])]
    public function createUser(
        UserDTO $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        GroupRepository $groupRepository,
    ): JsonResponse
    {
        try {
            $existsOne = $userRepository->findOneByEmail($request->getEmail());
            if ($existsOne) {
                return $this->json(['error' => self::EMAIL_ALREADY_TAKEN], Response::HTTP_BAD_GATEWAY);
            }

            $user = new User();
            $user->setName($request->getName());
            $user->setEmail($request->getEmail());

            $entityManager->persist($user);

            $groupIds = $request->getGroups();
            if (!empty($groupIds)) {
                foreach ($groupIds as $groupId) {
                    $group = $groupRepository->find($groupId);
                    if ($group) {
                        $user->addGroup($group);
                    }
                }
            }

            $entityManager->flush();

            $data = [
                'message'   => self::ENTITY_HAS_BEEN_CREATED,
                'data'      => $user,
            ];

            return $this->json($data, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $this->unprocessableExceptionMessage($e)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('users/{id}', name: '_update-user', methods: ['PUT'])]
    public function updateUser(
        UserDTO $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        int $id
    ): JsonResponse
    {
        try {
            $user = $userRepository->find($id);
            if (!$user) {
                return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }

            $user->setName($request->getName());

            $groupIds = $request->getGroups();
            if (!empty($groupIds)) {
                $user->removeGroups();
                foreach ($groupIds as $groupId) {
                    $group = $groupRepository->find($groupId);
                    if ($group) {
                        $user->addGroup($group);
                    }
                }
            }

            $entityManager->flush();

            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_UPDATED,
                'data'      => $user,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $this->unprocessableExceptionMessage($e)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('users/{id}', name: '_delete-user', methods: ['DELETE'])]
    public function deleteUser(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        int $id
    ): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            return $this->json(['error' => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => self::ENTITY_HAS_BEEN_DELETED]);
    }
}