<?php

namespace App\Entity;

use App\Dto\UserInput;
use App\Dto\UserOutput;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: 'email', message: 'Email already taken')]
#[ApiResource(
    denormalizationContext: ['groups' => ['write']],
    formats: ['json', 'jsonld'],
    input: UserInput::class,
    normalizationContext: ['groups' => ['read'], 'skip_null_values' => null],
    output: UserOutput::class)
]
class User implements \JsonSerializable, EntityInterface
{
    #[Groups(['read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'string', length: 255)]
    public ?string $name;

    #[Groups(['read', 'write'])]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public ?string $email;

    #[Groups(['read', 'write'])]
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'users')]
    #[ORM\JoinTable(name: 'users_groups')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    public Collection $groups;

    public function __construct(?string $name=null, ?string $email=null, ...$groups)
    {
        $this->name = $name;
        $this->email = $email;
        $this->groups = new ArrayCollection($groups);
    }

    public function __toString(): string
    {
        return trim($this->getName().' '.$this->getEmail());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->groups->contains($group)) {
            $this->groups->removeElement($group);
        }

        return $this;
    }

    public function removeGroups(): self
    {
        $this->groups->clear();
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'name'      => $this->getName(),
            'email'     => $this->getEmail(),
            'groups'    => $this->getGroupsToArray(),
        ];
    }

    public function getGroupsToArray(): array
    {
        return array_map(function (Group $group) {
            return [
                'id'    => $group->getId(),
                'name'  => $group->getName(),
            ];
        }, $this->getGroups()->toArray());
    }
}
