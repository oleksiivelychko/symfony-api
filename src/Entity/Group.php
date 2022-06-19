<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Dto\GroupInput;
use App\Dto\GroupOutput;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`groups`')]
#[ApiResource(
    denormalizationContext: ['groups' => ['write']],
    formats: ['json', 'jsonld'],
    input: GroupInput::class,
    normalizationContext: ['groups' => ['read']],
    output: GroupOutput::class,
)]
class Group implements \JsonSerializable
{
    #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Groups(['read','write'])]
    #[ORM\Column(type: 'string', length: 255)]
    public ?string $name = null;

    #[Groups(['read','write'])]
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'groups', fetch: 'EXTRA_LAZY')]
    public Collection $users;

    public function __construct(?string $name=null, ...$users)
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

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $user->addGroup($this); // synchronously updating inverse side
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $user->removeGroup($this); // synchronously updating inverse side
            $this->users->removeElement($user);
        }

        return $this;
    }

    public function removeUsers(): self
    {
        if (!$this->users->isEmpty()) {
            $this->users->clear();
        }

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'    => $this->getId(),
            'name'  => $this->getName(),
            'users' => $this->getUsersToArray()
        ];
    }

    public function getUsersToArray(): array
    {
        return array_map(function (User $user) {
            return [
                'id'    => $user->getId(),
                'name'  => $user->getName(),
                'email' => $user->getEmail(),
            ];
        }, $this->getUsers()->toArray());
    }
}
