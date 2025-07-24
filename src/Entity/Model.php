<?php

namespace App\Entity;

use App\Repository\ModelRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ModelRepository::class)]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom du modèle est requis.")]
    #[Assert\Length(max: 255, maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $name = null;

    #[ORM\Column(length: 300)]
    #[Assert\NotBlank(message: "Une image est requise.")]
    #[Assert\Length(max: 300, maxMessage: "Le nom du fichier image ne peut pas dépasser {{ limit }} caractères.")]
    private ?string $image = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix est requis.")]
    #[Assert\PositiveOrZero(message: "Le prix ne peut pas être négatif.")]
    private ?string $prix = null;

    #[ORM\ManyToOne(inversedBy: 'model')]
    private ?User $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function getProduct(): ?User
    {
        return $this->product;
    }

    public function setProduct(?User $product): static
    {
        $this->product = $product;
        return $this;
    }
}
