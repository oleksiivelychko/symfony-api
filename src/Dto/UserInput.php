<?php

namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class UserInput
{
    #[Groups(['write'])]
    #[Assert\NotBlank(message: 'assert.name.not_blank')]
    #[Assert\Length(min: 2, max: 50)]
    public string $name;

    #[Groups(['write'])]
    #[Assert\Email]
    public string $email;

    #[Groups(['write'])]
    public array $groups = [];
}