<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class UserOutput
{
    #[Groups(['read'])]
    public int $id;

    #[Groups(['read'])]
    public string $name;

    #[Groups(['read'])]
    public string $email;

    #[Groups(['read'])]
    public array $groups = [];
}