<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class GroupInput
{
    #[Groups(['write'])]
    public string $name;

    #[Groups(['write'])]
    public array $users;
}