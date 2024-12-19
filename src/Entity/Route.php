<?php

namespace App\Entity;

use App\Repository\RouteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RouteRepository::class)]
class Route
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, Coordinates>
     */
    #[ORM\OneToMany(targetEntity: Coordinates::class, mappedBy: 'route', orphanRemoval: true)]
    private Collection $coordinates;

    public function __construct()
    {
        $this->coordinates = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Coordinates>
     */
    public function getCoordinates(): Collection
    {
        return $this->coordinates;
    }

    public function addCoordinate(Coordinates $coordinate): static
    {
        if (!$this->coordinates->contains($coordinate)) {
            $this->coordinates->add($coordinate);
            $coordinate->setRoute($this);
        }

        return $this;
    }

    public function removeCoordinate(Coordinates $coordinate): static
    {
        if ($this->coordinates->removeElement($coordinate)) {
            // set the owning side to null (unless already changed)
            if ($coordinate->getRoute() === $this) {
                $coordinate->setRoute(null);
            }
        }

        return $this;
    }
}
