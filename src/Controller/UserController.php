<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api-v2', name: 'user-api')]
final class UserController extends RestfulController
{
    #[Route('users', name: 'fetch-users', methods: ['GET'])]
    public function fetchUsers(UserRepository $userRepository): JsonResponse
    {
        $data = $userRepository->findAll();
        return $this->json($data);
    }

    #[Route('users', name: 'fetch-user', methods: ['GET'])]
    public function fetchUser(UserRepository $userRepository, ?int $id=null): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => self::USER_NOT_FOUND,
            ];
            return $this->json($data, Response::HTTP_NOT_FOUND);
        }
        return $this->json($user);
    }

    #[Route('users', name: 'add-user', methods: ['POST'])]
    public function addUser(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $request = $this->transformJsonBody($request);
            if (!$request->get('name') || !$request->get('email')) {
                throw new \Exception();
            }

            if (!filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
                $data = [
                    'status' => Response::HTTP_BAD_GATEWAY,
                    'errors' => self::EMAIL_IS_NOT_VALID,
                ];
                return $this->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $existsOne = $entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));
            if ($existsOne) {
                $data = [
                    'status' => Response::HTTP_BAD_GATEWAY,
                    'errors' => self::EMAIL_ALREADY_TAKEN,
                ];
                return $this->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $user = new User();
            $user->setName($request->get('name'));
            $user->setEmail($request->get('email'));
            $entityManager->persist($user);

            if ($request->get('groups')) {
                foreach ($request->get('groups') as $groupId) {
                    $group = $entityManager->getRepository(Group::class)->find($groupId);
                    if ($group) {
                        $user->addGroup($group);
                    }
                }
            }

            $entityManager->flush();

            $data = [
                'status'    => Response::HTTP_CREATED,
                'success'   => self::USER_HAS_BEEN_CREATED,
                'id'        => $user->getId(),
                'name'      => $user->getName(),
                'email'     => $user->getEmail(),
            ];
            return $this->json($data);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => self::UNPROCESSABLE_ENTITY,
                'message' => $e->getMessage()
            ];
            return $this->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('users', name: 'edit-user', methods: ['PUT'])]
    public function editUser(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        int $id
    ): JsonResponse
    {
        try {
            $user = $userRepository->find($id);
            if (!$user) {
                $data = [
                    'status' => Response::HTTP_NOT_FOUND,
                    'errors' => self::USER_NOT_FOUND,
                ];
                return $this->json($data, Response::HTTP_NOT_FOUND);
            }

            $request = $this->transformJsonBody($request);
            $user->setName($request->get('name'));

            if ($request->get('groups')) {
                $user->removeGroups();
                foreach ($request->get('groups') as $groupId) {
                    $group = $groupRepository->find($groupId);
                    if ($group) {
                        $user->addGroup($group);
                    }
                }
            }

            $entityManager->flush();

            $data = [
                'status'    => Response::HTTP_OK,
                'success'   => self::USER_HAS_BEEN_UPDATED,
                'id'        => $user->getId(),
                'name'      => $user->getName()
            ];
            return $this->json($data);

        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => self::UNPROCESSABLE_ENTITY,
                'message' => $e->getMessage()
            ];
            return $this->json($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('users', name: 'remove-user', methods: ['DELETE'])]
    public function removeUser(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        ?int $id=null
    ): JsonResponse
    {
        $user = $userRepository->find($id);
        if (!$user) {
            $data = [
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => self::USER_NOT_FOUND
            ];
            return $this->json($data, Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json([
            'status' => Response::HTTP_OK
        ]);
    }
}