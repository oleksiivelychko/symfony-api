<?php

namespace App\Controller;

use App\Entity\Group;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api-v2', name: 'group-api')]
final class GroupController extends RestfulController
{
    #[Route('groups', name: 'fetch-groups', methods: ['GET'])]
    public function fetchGroups(GroupRepository $groupRepository): JsonResponse
    {
        /**
         * @var Group $group
         */
        foreach ($groupRepository->findWithUsers() as $group) {
            $data[] = $group->getData();
        }

        return $this->json($data ?? []);
    }

    #[Route('groups', name: 'fetch-group', methods: ['GET'])]
    public function fetchGroup(GroupRepository $groupRepository, int $id): JsonResponse
    {
        $group = $groupRepository->find($id);
        if (!$group) {
            return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json([$group->getData()]);
    }

    #[Route('groups', name: 'add-group', methods: ['POST'])]
    public function addGroup(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $request = $this->transformJsonBody($request);
            if (!$request->get('name')) {
                throw new \Exception();
            }

            $group = new Group();
            $group->setName($request->get('name'));
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_CREATED,
                'id'        => $group->getId(),
                'name'      => $group->getName()
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $this->unprocessableExceptionMessage($e)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('groups', name: 'edit-group', methods: ['PUT'])]
    public function editGroup(
        Request $request,
        EntityManagerInterface $entityManager,
        GroupRepository $groupRepository,
        int $id
    ): JsonResponse
    {
        try {
            $group = $groupRepository->find($id);
            if (!$group) {
                return $this->json(['error' => self::ENTITY_NOT_FOUND], Response::HTTP_NOT_FOUND);
            }

            $request = $this->transformJsonBody($request);
            if (!$request->get('name')) {
                throw new \Exception();
            }

            $entityManager->flush();

            return $this->json([
                'message'   => self::ENTITY_HAS_BEEN_UPDATED,
                'id'        => $group->getId(),
                'name'      => $group->getName()
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $this->unprocessableExceptionMessage($e)
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('groups', name: 'delete-group', methods: ['DELETE'])]
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