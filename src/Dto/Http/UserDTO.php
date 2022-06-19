<?php

namespace App\Dto\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class UserDTO implements RequestDTOInterface
{
    #[Assert\NotNull(message: 'assert.name.is_null')]
    #[Assert\NotBlank(message: 'assert.name.not_blank')]
    #[Assert\Length(min: 2, max: 50)]
    private string $name;

    #[Assert\Email]
    private string $email;

    #[Assert\Type('array')]
    #[Assert\All(
       new Assert\Type(['type' => 'int']),
    )]
    private array $groups;

    public function __construct(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $this->name = $content['name'] ?? '';
        $this->email = $content['email'] ?? '';
        $this->groups = $content['groups'] ?? [];
    }

    public function asObject(): object
    {
        $object = new \stdClass();
        $object->name = $this->name;
        $object->email = $this->email;
        $object->groups = $this->groups;

        return $object;
    }
}