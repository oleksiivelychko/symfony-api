<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Dto\UserInput;
use App\Entity\User;
use App\Repository\GroupRepository;
use Exception;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class UserInputDataTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    private GroupRepository $groupRepository;

    public function __construct(ValidatorInterface $validator, GroupRepository $groupRepository)
    {
        $this->validator = $validator;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): User
    {
        $this->validator->validate($object);

        /**
         * @var User $user
         * @var UserInput $object
         */
        $user = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;

        if (!$user && !isset($object->email)) {
            throw new Exception('Email field is required');
        }

        if (!$user) {
            $user = new User();
            $user->setEmail($object->email);
        }

        $user->setName($object->name);

        if (isset($object->groups) && count($object->groups) > 0) {
            $user->removeGroups();
            foreach ($object->groups as $userId) {
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