<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\UserOutput;
use App\Entity\User;

final class UserOutputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = []): UserOutput
    {
        /**
         * @var User $object
         */
        $output = new UserOutput();
        $output->id = $object->getId();
        $output->name = $object->getName();
        $output->email = $object->getEmail();

        if (!$object->groups->isEmpty()) {
            $output->groups = $object->getGroupsToArray();
        }

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return UserOutput::class === $to && $data instanceof User;
    }
}