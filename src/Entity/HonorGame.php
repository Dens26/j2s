<?php

namespace App\Entity;

use App\Repository\HonorGameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HonorGameRepository::class)]
class HonorGame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    private ?string $year = null;

    #[ORM\ManyToOne(inversedBy: 'honorGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Honor $honor = null;

    #[ORM\ManyToOne(inversedBy: 'honorGames')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getHonor(): ?honor
    {
        return $this->honor;
    }

    public function setHonor(?honor $honor): static
    {
        $this->honor = $honor;

        return $this;
    }

    public function getGame(): ?game
    {
        return $this->game;
    }

    public function setGame(?game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function __toString()
    {
        return $this->honor;
    }
}
