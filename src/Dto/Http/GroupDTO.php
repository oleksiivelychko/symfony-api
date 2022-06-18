<?php

namespace App\Dto\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

final class GroupDTO implements RequestDTOInterface
{
    #[Assert\NotNull]
    #[Assert\NotBlank]
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}