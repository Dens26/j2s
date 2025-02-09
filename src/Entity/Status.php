<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, MysteryGame>
     */
    #[ORM\OneToMany(targetEntity: MysteryGame::class, mappedBy: 'status')]
    private Collection $mysteryGames;

    public function __construct()
    {
        $this->mysteryGames = new ArrayCollection();
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
     * @return Collection<int, MysteryGame>
     */
    public function getMysteryGames(): Collection
    {
        return $this->mysteryGames;
    }

    public function addMysteryGame(MysteryGame $mysteryGame): static
    {
        if (!$this->mysteryGames->contains($mysteryGame)) {
            $this->mysteryGames->add($mysteryGame);
            $mysteryGame->setStatus($this);
        }

        return $this;
    }

    public function removeMysteryGame(MysteryGame $mysteryGame): static
    {
        if ($this->mysteryGames->removeElement($mysteryGame)) {
            // set the owning side to null (unless already changed)
            if ($mysteryGame->getStatus() === $this) {
                $mysteryGame->setStatus(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
