<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        "post" => [
            'path' => '/users/confirmation',
        ]
    ],
    itemOperations: []
)]
class UserConfirmation
{

    #[Assert\NotBlank()]
    #[Assert\Length(min: 30, max: 30)]
    private ?string $confirmationToken;

    #[Assert\NotBlank()]
    #[Assert\Regex(
        pattern: '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{8,20}$/'
    )]
    private ?string $password;

    #[Assert\NotBlank()]
    #[Assert\Expression(
        "this.getPassword() === this.getRetypedPassword()",message: "Password does not match."
    )]
    private ?string $retypedPassword;


    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
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