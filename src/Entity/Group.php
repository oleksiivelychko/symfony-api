<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`groups`')]
#[ApiResource]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "The name '{{ value }}' is empty.")]
    public string $name;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups')]
    public Collection $users;

    public function __construct(string $name, ...$users)
    {
        $this->name = $name;
        $this->users = new ArrayCollection($users);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName()
        ];
    }

    public function getData(): array
    {
        return [
            'id'    => $this->getId(),
            'name'  => $this->getName(),
            'users' => array_map(function (User $user) {
                return [
                    'id'    => $user->getId(),
                    'name'  => $user->getName(),
                    'email' => $user->getEmail(),
                ];
            }, $this->getUsers()->toArray())
        ];
    }
}
