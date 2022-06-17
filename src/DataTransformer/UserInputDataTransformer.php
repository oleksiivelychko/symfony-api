<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\UserInput;
use App\Entity\User;
use App\Repository\GroupRepository;
use Exception;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class UserInputDataTransformer implements DataTransformerInterface
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): User
    {
        if ($object->email === null) {
            throw new Exception('Email is required');
        }

        /**
         * @var User $user
         * @var UserInput $object
         */
        $user = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$user) {
            $user = new User();
        }

        $user->setName($object->name);
        $user->setEmail($object->email);

        if (isset($object->users) && count($object->users) > 0) {
            $user->removeGroups();
            foreach ($object->users as $userId) {
                $group = $this->groupRepository->find($userId);
                if ($group) {
                    $user->addGroup($group);
                }
            }
        }

        return $user;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof User) {
            return false;
        }

        return User::class === $to && null !== ($context['input']['class'] ?? null);
    }
}