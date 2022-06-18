<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\GroupInput;
use App\Entity\Group;
use App\Repository\UserRepository;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use ApiPlatform\Core\Validator\ValidatorInterface;

final class GroupInputDataTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    private UserRepository $userRepository;

    public function __construct(ValidatorInterface $validator, UserRepository $userRepository)
    {
        $this->validator = $validator;
        $this->userRepository = $userRepository;
    }

    public function transform($object, string $to, array $context = []): Group
    {
        $this->validator->validate($object);

        /**
         * @var Group $group
         * @var GroupInput $object
         */
        $group = $context[AbstractNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$group) {
            $group = new Group();
        }

        $group->setName($object->name);

        if (isset($object->users) && count($object->users) > 0) {
            $group->removeUsers();
            foreach ($object->users as $userId) {
                $user = $this->userRepository->find($userId);
                if ($user) {
                    $group->addUser($user);
                }
            }
        }

        return $group;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        // in the case of an input, the value given here is an array (the JSON decoded).
        // if it's a group we transformed the data already
        if ($data instanceof Group) {
            return false;
        }

        return Group::class === $to && null !== ($context['input']['class'] ?? null);
    }
}