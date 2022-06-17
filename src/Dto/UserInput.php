<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class UserInput
{
    #[Groups(['write'])]
    public string $name;

    #[Groups(['write'])]
    public string $email;

    #[Groups(['write'])]
    public array $groups = [];
}