<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class GroupOutput
{
    #[Groups(['read'])]
    public int $id;

    #[Groups(['read'])]
    public string $name;

    #[Groups(['read'])]
    public array $users;
}