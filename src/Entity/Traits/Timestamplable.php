<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait Timestamplable
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null ;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }


    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    #[
        ORM\PrePersist,
        ORM\PreUpdate
    ]
    public function updateTimestamps(): void
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable);
        }

        $this->setUpdatedAt(new \DateTimeImmutable);
    }
}