<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\Timestamplable;
use App\Repository\ProfileRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'profiles')]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'security' => "is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')",
            'denormalization_context' => ['groups' => ['profile:post:write']]
        ]
    ],
    itemOperations: [
        'get' =>[
            'security' => "is_granted('ROLE_ADMIN') or object.getId() == user.getProfile().getId()"
        ],
        'put' => [
            'security' => "is_granted('ROLE_ADMIN') or object.getId() == user.getProfile().getId()",
            'denormalization_context' => ['groups' => ['profile:put:write']]
        ],
        'delete'
    ],
    attributes: [
        'security' => "is_granted('ROLE_ADMIN')"
    ]
)]
class Profile
{
    use Timestamplable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    #[Groups('user:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['user:read','profile:post:write'])]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    #[Groups(['user:read','profile:post:write'])]
    private ?string $firstname = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Groups(['user:read','profile:post:write'])]
    private ?string $gender = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[Groups(['user:read','profile:post:write','profile:put:write'])]
    private ?string $address = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['user:read','profile:post:write'])]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 30)]
    #[Assert\Length(min: 10, max: 30)]
    #[Groups(['user:read','profile:post:write','profile:put:write'])]
    private ?string $phone = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['profile:post:write'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFullName()
    {
        return $this->getFirstname().' '. $this->getLastname();
    }

    public function __toString(): string
    {
        $this->getFullName();
    }
}
