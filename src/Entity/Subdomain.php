<?php

namespace App\Entity;

use App\Repository\SubdomainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubdomainRepository::class)]
class Subdomain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, game>
     */
    #[ORM\ManyToMany(targetEntity: game::class, inversedBy: 'subdomains')]
    private Collection $game;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $translatedName = null;

    public function __construct()
    {
        $this->game = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, game>
     */
    public function getGame(): Collection
    {
        return $this->game;
    }

    public function addGame(game $game): static
    {
        if (!$this->game->contains($game)) {
            $this->game->add($game);
        }

        return $this;
    }

    public function removeGame(game $game): static
    {
        $this->game->removeElement($game);

        return $this;
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

    public function getTranslatedName(): ?string
    {
        return $this->translatedName;
    }

    public function setTranslatedName(string $translatedName): static
    {
        $this->translatedName = $translatedName;

        return $this;
    }
}
