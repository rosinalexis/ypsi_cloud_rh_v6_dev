<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\Timestamplable;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity('email')]
#[ApiResource(
    collectionOperations: [
        'get',
        'post',
    ],
    itemOperations: [
        'get',
        'put',
        'delete'
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_USER = ["ROLE_USER"];
    const ROLE_ADMIN = ["ROLE_ADMIN"];

    use Timestamplable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column()]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(min: 6, max: 180)]
    private ?string $email = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Choice([self::ROLE_USER, self::ROLE_ADMIN])]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/"
    )]
    private ?string $password = null;

    #[Assert\NotBlank]
    #[Assert\Expression(
        "this.getPassword() === this.getRetypedPassword()",message: "Password does not match."
    )]
    private ?string $retypedPassword = null;

    #[ORM\Column]
    #[Assert\Type('bool')]
    private ?bool $blocked = false;

    #[ORM\Column]
    #[Assert\Type('bool')]
    private ?bool $confirmed = false;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Company $company = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Job $job = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isBlocked(): ?bool
    {
        return $this->blocked;
    }

    public function setBlocked(bool $blocked): self
    {
        $this->blocked = $blocked;

        return $this;
    }

    public function isConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        // unset the owning side of the relation if necessary
        if ($profile === null && $this->profile !== null) {
            $this->profile->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($profile !== null && $profile->getUser() !== $this) {
            $profile->setUser($this);
        }

        $this->profile = $profile;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(Job $job): self
    {
        // set the owning side of the relation if necessary
        if ($job->getUser() !== $this) {
            $job->setUser($this);
        }

        $this->job = $job;

        return $this;
    }


    public function getRetypedPassword(): ?string
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword(?string $retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }
}
