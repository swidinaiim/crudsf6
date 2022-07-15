<?php

namespace App\Entity;

use App\Repository\PerrsonneRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerrsonneRepository::class)]
class Perrsonne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private $job;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): self
    {
        $this->job = $job;

        return $this;
    }
}
