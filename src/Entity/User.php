<?php

namespace App\Entity;

use App\Dto\UserInput;
use App\Dto\UserOutput;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
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
class User implements
    \JsonSerializable,
    EntityInterface,
    UserInterface,
    PasswordAuthenticatedUserInterface
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
    #[ORM\Column(type: 'json')]
    private $roles = [];

    /**
     * @var string The hashed password
     */
    #[Groups(['read'])]
    #[ORM\Column(type: 'string')]
    private $password;

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

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @deprecated
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->getId(),
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
