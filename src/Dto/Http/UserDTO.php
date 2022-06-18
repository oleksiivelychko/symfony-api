<?php

namespace App\Dto\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class UserDTO implements RequestDTOInterface
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }
}