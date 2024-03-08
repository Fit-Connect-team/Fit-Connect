<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $cite = null;

    #[ORM\ManyToOne(inversedBy: 'Adresses')]
    private ?Livraison $Livraison = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCite(): ?string
    {
        return $this->cite;
    }

    public function setCite(string $Cite): static
    {
        $this->cite = $Cite;

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->Livraison;
    }

    public function setLivraison(?Livraison $Livraison): static
    {
        $this->Livraison = $Livraison;

        return $this;
    }

}
