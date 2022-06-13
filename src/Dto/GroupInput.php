<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class GroupInput
{
    #[Groups(['read', 'write'])]
    public string $name;

    #[Groups(['read', 'write'])]
    public array $users;
}