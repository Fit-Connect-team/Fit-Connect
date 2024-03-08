<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $status = null;


    #[ORM\ManyToOne(inversedBy: 'Livraisons')]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(inversedBy: 'Livraisons')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getstatus(): ?int
    {
        return $this->status;
    }

    public function setstatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }


    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $Produit): static
    {
        $this->produit = $Produit;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $User): static
    {
        $this->user = $User;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id; 
    }
}
