<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[UniqueEntity('gameId')]
#[ORM\UniqueConstraint(name: 'UNIQ_GAME_GAMEID', fields: ['gameId'])]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $gameId = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allNames = null;

    #[ORM\Column(nullable: true)]
    private ?int $yearPublished = null;

    #[ORM\Column(nullable: true)]
    private ?int $minPlayers = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxPlayers = null;

    #[ORM\Column(nullable: true)]
    private ?int $playingTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'game')]
    private Collection $categories;

    /**
     * @var Collection<int, Developer>
     */
    #[ORM\ManyToMany(targetEntity: Developer::class, mappedBy: 'game')]
    private Collection $developers;

    /**
     * @var Collection<int, Designer>
     */
    #[ORM\ManyToMany(targetEntity: Designer::class, mappedBy: 'game')]
    private Collection $designers;

    /**
     * @var Collection<int, Family>
     */
    #[ORM\ManyToMany(targetEntity: Family::class, mappedBy: 'game')]
    private Collection $families;

    /**
     * @var Collection<int, Mechanic>
     */
    #[ORM\ManyToMany(targetEntity: Mechanic::class, mappedBy: 'game')]
    private Collection $mechanics;

    /**
     * @var Collection<int, Subdomain>
     */
    #[ORM\ManyToMany(targetEntity: Subdomain::class, mappedBy: 'game')]
    private Collection $subdomains;

    /**
     * @var Collection<int, Artist>
     */
    #[ORM\ManyToMany(targetEntity: Artist::class, mappedBy: 'game')]
    private Collection $artists;

    /**
     * @var Collection<int, Publisher>
     */
    #[ORM\ManyToMany(targetEntity: Publisher::class, mappedBy: 'game')]
    private Collection $publishers;

    /**
     * @var Collection<int, GraphicDesigner>
     */
    #[ORM\ManyToMany(targetEntity: GraphicDesigner::class, mappedBy: 'game')]
    private Collection $graphicDesigners;

    /**
     * @var Collection<int, HonorGame>
     */
    #[ORM\OneToMany(targetEntity: HonorGame::class, mappedBy: 'game')]
    private Collection $honorGames;

    #[ORM\Column]
    private ?bool $checked = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->developers = new ArrayCollection();
        $this->designers = new ArrayCollection();
        $this->families = new ArrayCollection();
        $this->mechanics = new ArrayCollection();
        $this->subdomains = new ArrayCollection();
        $this->artists = new ArrayCollection();
        $this->publishers = new ArrayCollection();
        $this->graphicDesigners = new ArrayCollection();
        $this->honorGames = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameId(): ?int
    {
        return $this->gameId;
    }

    public function setGameId(int $gameId): static
    {
        $this->gameId = $gameId;

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

    public function getAllNames(): ?string
    {
        return $this->allNames;
    }

    public function setAllNames(?string $allNames): static
    {
        $this->allNames = $allNames;

        return $this;
    }

    public function getYearPublished(): ?int
    {
        return $this->yearPublished;
    }

    public function setYearPublished(?int $yearPublished): static
    {
        $this->yearPublished = $yearPublished;

        return $this;
    }

    public function getMinPlayers(): ?int
    {
        return $this->minPlayers;
    }

    public function setMinPlayers(?int $minPlayers): static
    {
        $this->minPlayers = $minPlayers;

        return $this;
    }

    public function getMaxPlayers(): ?int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): static
    {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    public function getPlayingTime(): ?int
    {
        return $this->playingTime;
    }

    public function setPlayingTime(?int $playingTime): static
    {
        $this->playingTime = $playingTime;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addGame($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Developer>
     */
    public function getDevelopers(): Collection
    {
        return $this->developers;
    }

    public function addDeveloper(Developer $developer): static
    {
        if (!$this->developers->contains($developer)) {
            $this->developers->add($developer);
            $developer->addGame($this);
        }

        return $this;
    }

    public function removeDeveloper(Developer $developer): static
    {
        if ($this->developers->removeElement($developer)) {
            $developer->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Designer>
     */
    public function getDesigners(): Collection
    {
        return $this->designers;
    }

    public function addDesigner(Designer $designer): static
    {
        if (!$this->designers->contains($designer)) {
            $this->designers->add($designer);
            $designer->addGame($this);
        }

        return $this;
    }

    public function removeDesigner(Designer $designer): static
    {
        if ($this->designers->removeElement($designer)) {
            $designer->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Family>
     */
    public function getFamilies(): Collection
    {
        return $this->families;
    }

    public function addFamily(Family $family): static
    {
        if (!$this->families->contains($family)) {
            $this->families->add($family);
            $family->addGame($this);
        }

        return $this;
    }

    public function removeFamily(Family $family): static
    {
        if ($this->families->removeElement($family)) {
            $family->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Mechanic>
     */
    public function getMechanics(): Collection
    {
        return $this->mechanics;
    }

    public function addMechanic(Mechanic $mechanic): static
    {
        if (!$this->mechanics->contains($mechanic)) {
            $this->mechanics->add($mechanic);
            $mechanic->addGame($this);
        }

        return $this;
    }

    public function removeMechanic(Mechanic $mechanic): static
    {
        if ($this->mechanics->removeElement($mechanic)) {
            $mechanic->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Subdomain>
     */
    public function getSubdomains(): Collection
    {
        return $this->subdomains;
    }

    public function addSubdomain(Subdomain $subdomain): static
    {
        if (!$this->subdomains->contains($subdomain)) {
            $this->subdomains->add($subdomain);
            $subdomain->addGame($this);
        }

        return $this;
    }

    public function removeSubdomain(Subdomain $subdomain): static
    {
        if ($this->subdomains->removeElement($subdomain)) {
            $subdomain->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Artist>
     */
    public function getArtists(): Collection
    {
        return $this->artists;
    }

    public function addArtist(Artist $artist): static
    {
        if (!$this->artists->contains($artist)) {
            $this->artists->add($artist);
            $artist->addGame($this);
        }

        return $this;
    }

    public function removeArtist(Artist $artist): static
    {
        if ($this->artists->removeElement($artist)) {
            $artist->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Publisher>
     */
    public function getPublishers(): Collection
    {
        return $this->publishers;
    }

    public function addPublisher(Publisher $publisher): static
    {
        if (!$this->publishers->contains($publisher)) {
            $this->publishers->add($publisher);
            $publisher->addGame($this);
        }

        return $this;
    }

    public function removePublisher(Publisher $publisher): static
    {
        if ($this->publishers->removeElement($publisher)) {
            $publisher->removeGame($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, GraphicDesigner>
     */
    public function getGraphicDesigners(): Collection
    {
        return $this->graphicDesigners;
    }

    public function addGraphicDesigner(GraphicDesigner $graphicDesigner): static
    {
        if (!$this->graphicDesigners->contains($graphicDesigner)) {
            $this->graphicDesigners->add($graphicDesigner);
            $graphicDesigner->addGame($this);
        }

        return $this;
    }

    public function removeGraphicDesigner(GraphicDesigner $graphicDesigner): static
    {
        if ($this->graphicDesigners->removeElement($graphicDesigner)) {
            $graphicDesigner->removeGame($this);
        }

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
            $honorGame->setGame($this);
        }

        return $this;
    }

    public function removeHonorGame(HonorGame $honorGame): static
    {
        if ($this->honorGames->removeElement($honorGame)) {
            // set the owning side to null (unless already changed)
            if ($honorGame->getGame() === $this) {
                $honorGame->setGame(null);
            }
        }

        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): static
    {
        $this->checked = $checked;

        return $this;
    }
}
