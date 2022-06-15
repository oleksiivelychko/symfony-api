<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\GroupOutput;
use App\Entity\Group;

final class GroupOutputDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = []): GroupOutput
    {
        /**
         * @var Group $object
         */
        $output = new GroupOutput();
        $output->id = $object->getId();
        $output->name = $object->getName();

        if (!$object->users->isEmpty()) {
            $output->users = $object->getUsersToArray();
        }

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return GroupOutput::class === $to && $data instanceof Group;
    }
}