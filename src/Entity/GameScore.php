<?php

namespace App\Entity;

use App\Repository\GameScoreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameScoreRepository::class)]
class GameScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = null;

    #[ORM\Column(nullable: true)]
    private ?int $progression = null;

    #[ORM\Column(length: 255)]
    private ?string $yearPublished = null;

    #[ORM\Column(length: 255)]
    private ?string $minPlayers = null;

    #[ORM\Column(length: 255)]
    private ?string $playingTime = null;

    #[ORM\Column(length: 255)]
    private ?string $age = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $categoriesIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $subdomainsIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $mechanicsIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $designersIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $artistsIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $graphicDesignersIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $honorsIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $publishersIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $develpersIndices = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $searchHistory = null;

    #[ORM\Column(length: 255)]
    private ?string $relation = null;

    #[ORM\ManyToOne(inversedBy: 'gameScores')]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'gameScores')]
    private ?Game $game = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getProgression(): ?int
    {
        return $this->progression;
    }

    public function setProgression(?int $progression): static
    {
        $this->progression = $progression;

        return $this;
    }

    public function getYearPublished(): ?string
    {
        return $this->yearPublished;
    }

    public function setYearPublished(string $yearPublished): static
    {
        $this->yearPublished = $yearPublished;

        return $this;
    }

    public function getMinPlayers(): ?string
    {
        return $this->minPlayers;
    }

    public function setMinPlayers(string $minPlayers): static
    {
        $this->minPlayers = $minPlayers;

        return $this;
    }

    public function getPlayingTime(): ?string
    {
        return $this->playingTime;
    }

    public function setPlayingTime(string $playingTime): static
    {
        $this->playingTime = $playingTime;

        return $this;
    }

    public function getAge(): ?string
    {
        return $this->age;
    }

    public function setAge(string $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getCategoriesIndices(): ?string
    {
        return $this->categoriesIndices;
    }

    public function setCategoriesIndices(string $categoriesIndices): static
    {
        $this->categoriesIndices = $categoriesIndices;

        return $this;
    }

    public function getSubdomainsIndices(): ?string
    {
        return $this->subdomainsIndices;
    }

    public function setSubdomainsIndices(string $subdomainsIndices): static
    {
        $this->subdomainsIndices = $subdomainsIndices;

        return $this;
    }

    public function getMechanicsIndices(): ?string
    {
        return $this->mechanicsIndices;
    }

    public function setMechanicsIndices(string $mechanicsIndices): static
    {
        $this->mechanicsIndices = $mechanicsIndices;

        return $this;
    }

    public function getDesignersIndices(): ?string
    {
        return $this->designersIndices;
    }

    public function setDesignersIndices(string $designersIndices): static
    {
        $this->designersIndices = $designersIndices;

        return $this;
    }

    public function getArtistsIndices(): ?string
    {
        return $this->artistsIndices;
    }

    public function setArtistsIndices(string $artistsIndices): static
    {
        $this->artistsIndices = $artistsIndices;

        return $this;
    }

    public function getGraphicDesignersIndices(): ?string
    {
        return $this->graphicDesignersIndices;
    }

    public function setGraphicDesignersIndices(string $graphicDesignersIndices): static
    {
        $this->graphicDesignersIndices = $graphicDesignersIndices;

        return $this;
    }

    public function getHonorsIndices(): ?string
    {
        return $this->honorsIndices;
    }

    public function setHonorsIndices(string $honorsIndices): static
    {
        $this->honorsIndices = $honorsIndices;

        return $this;
    }

    public function getPublishersIndices(): ?string
    {
        return $this->publishersIndices;
    }

    public function setPublishersIndices(string $publishersIndices): static
    {
        $this->publishersIndices = $publishersIndices;

        return $this;
    }

    public function getDevelpersIndices(): ?string
    {
        return $this->develpersIndices;
    }

    public function setDevelpersIndices(string $develpersIndices): static
    {
        $this->develpersIndices = $develpersIndices;

        return $this;
    }

    public function getSearchHistory(): ?string
    {
        return $this->searchHistory;
    }

    public function setSearchHistory(string $searchHistory): static
    {
        $this->searchHistory = $searchHistory;

        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): static
    {
        $this->relation = $relation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
