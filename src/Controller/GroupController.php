<?php

namespace App\Controller;

use App\Dto\Http\GroupDTO;
use App\Services\GroupService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api-v2/', name: 'group-api')]
final class GroupController extends RestfulController
{
    #[Route('groups', name: '_list-groups', methods: ['GET'])]
    public function listGroups(GroupService $groupService): JsonResponse
    {
        return $this->json($groupService->list());
    }

    #[Route('groups/{id}', name: '_get-group', methods: ['GET'])]
    public function getGroup(GroupService $groupService, int $id): JsonResponse
    {
        $group = $groupService->get($id);
        if ($group === null) {
            return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json($group);
    }

    #[Route('groups', name: '_create-group', methods: ['POST'])]
    public function createGroup(GroupDTO $request, GroupService $groupService): JsonResponse
    {
        try {
            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_CREATED,
                'data'      => $groupService->create($request->asObject()),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            return $this->json(['error' => $this->unprocessableExceptionMessage($e)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('groups/{id}', name: '_update-group', methods: ['PUT'])]
    public function updateGroup(GroupDTO $request, GroupService $groupService, int $id): JsonResponse
    {
        try {
            $group = $groupService->update($request->asObject(), $id);
            if ($group === null) {
                return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }

            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_UPDATED,
                'data'      => $group,
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => $this->unprocessableExceptionMessage($e)], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('groups/{id}', name: '_delete-group', methods: ['DELETE'])]
    public function deleteGroup(GroupService $groupService, int $id): JsonResponse
    {
        $group = $groupService->delete($id);
        if ($group === null) {
            return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['message' => self::ENTITY_HAS_BEEN_DELETED]);
    }
}