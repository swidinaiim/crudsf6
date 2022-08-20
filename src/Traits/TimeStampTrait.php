<?php

namespace App\Traits;
use Doctrine\ORM\Mapping as ORM;

trait TimeStampTrait
{

    #[ORM\Column(nullable: true)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    

    #[ORM\PrePersist]
    public function onPrePersist(){
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

    }

    #[ORM\PreUpdate]
    public function onPreUpdate(){
        $this->updatedAt = new \DateTime();
    }
}
