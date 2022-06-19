<?php

namespace App\Controller;

use App\Dto\Http\UserDTO;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api-v2/', name: 'user-api')]
final class UserController extends RestfulController
{
    #[Route('users', name: '_list-users', methods: ['GET'])]
    public function listUsers(UserService $userService): JsonResponse
    {
        return $this->json($userService->list());
    }

    #[Route('users/{id}', name: '_fetch-user', methods: ['GET'])]
    public function fetchUser(UserService $userService, int $id): JsonResponse
    {
        $user = $userService->get($id);
        if ($user === null) {
            return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json($user);
    }

    #[Route('users', name: '_create-user', methods: ['POST'])]
    public function createUser(UserDTO $request, UserService $userService): JsonResponse
    {
        try {
            $user = $userService->create($request->asObject());
            if ($user === null) {
                return $this->json(['error' => self::EMAIL_ALREADY_TAKEN], Response::HTTP_BAD_GATEWAY);
            }

            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_CREATED,
                'data'      => $user,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json([
                'error' => $this->unprocessableExceptionMessage($e)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('users/{id}', name: '_update-user', methods: ['PUT'])]
    public function updateUser(UserDTO $request, UserService $userService, int $id): JsonResponse
    {
        try {
            $user = $userService->update($request->asObject(), $id);
            if ($user === null) {
                return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }

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
    public function deleteUser(UserService $userService, int $id): JsonResponse
    {
        $user = $userService->delete($id);
        if ($user === null) {
            return $this->json(['error' => Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => self::ENTITY_HAS_BEEN_DELETED]);
    }
}