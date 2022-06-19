<?php

namespace App\Dto\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class GroupDTO implements RequestDTOInterface
{
    #[Assert\NotBlank(message: 'assert.name.not_blank')]
    #[Assert\Length(min: 2, max: 50)]
    private string $name;

    #[Assert\Type('array')]
    #[Assert\All(
       new Assert\Type(['type' => 'int']),
    )]
    private array $users;

    public function __construct(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $this->name = $content['name'] ?? '';
        $this->users = $content['users'] ?? [];
    }

    public function asObject(): object
    {
        $object = new \stdClass();
        $object->name = $this->name;
        $object->users = $this->users;

        return $object;
    }
}