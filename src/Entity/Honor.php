<?php

namespace App\Entity;

use App\Repository\HonorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HonorRepository::class)]
class Honor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, HonorGame>
     */
    #[ORM\OneToMany(targetEntity: HonorGame::class, mappedBy: 'honor')]
    private Collection $honorGames;

    public function __construct()
    {
        $this->honorGames = new ArrayCollection();
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

    /**
     * @return Collection<int, HonorGame>
     */
    public function getHonorGames(): Collection
    {
        return $this->honorGames;
    }

    public function addHonorGame(HonorGame $honorGame): static
    {
        if (!$this->honorGames->contains($honorGame)) {
            $this->honorGames->add($honorGame);
            $honorGame->setHonor($this);
        }

        return $this;
    }

    public function removeHonorGame(HonorGame $honorGame): static
    {
        if ($this->honorGames->removeElement($honorGame)) {
            // set the owning side to null (unless already changed)
            if ($honorGame->getHonor() === $this) {
                $honorGame->setHonor(null);
            }
        }

        return $this;
    }
}
