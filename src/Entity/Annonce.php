<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnnonceRepository::class)]
class Annonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\Column(length: 255)]
    private ?int $salary = null;

    #[ORM\Column(length: 45)]
    private ?string $contractType = null;

    #[ORM\Column(length: 45)]
    private ?string $studyLevel = null;

    #[ORM\Column(nullable: true)]
    private ?bool $remote = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $workTime = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $publicationStatus = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endingAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    #[ORM\ManyToOne(inversedBy: 'annonces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExternaticConsultant $author = null;

    #[ORM\OneToMany(mappedBy: 'annonce', targetEntity: RecrutementProcess::class, orphanRemoval: true)]
    private Collection $recrutementProcesses;

    #[ORM\ManyToMany(targetEntity: Candidat::class, inversedBy: 'annonces')]
    private Collection $favorite;

    #[ORM\ManyToMany(targetEntity: Techno::class, inversedBy: 'annonces')]
    private Collection $techno;

    public function __construct()
    {
        $this->recrutementProcesses = new ArrayCollection();
        $this->favorite = new ArrayCollection();
        $this->techno = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(string $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): self
    {
        $this->contractType = $contractType;

        return $this;
    }

    public function getStudyLevel(): ?string
    {
        return $this->studyLevel;
    }

    public function setStudyLevel(string $studyLevel): self
    {
        $this->studyLevel = $studyLevel;

        return $this;
    }

    public function isRemote(): ?bool
    {
        return $this->remote;
    }

    public function setRemote(?bool $remote): self
    {
        $this->remote = $remote;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWorkTime(): ?string
    {
        return $this->workTime;
    }

    public function setWorkTime(?string $workTime): self
    {
        $this->workTime = $workTime;

        return $this;
    }

    public function getPublicationStatus(): ?string
    {
        return $this->publicationStatus;
    }

    public function setPublicationStatus(?string $publicationStatus): self
    {
        $this->publicationStatus = $publicationStatus;

        return $this;
    }

    public function getEndingAt(): ?\DateTimeInterface
    {
        return $this->endingAt;
    }

    public function setEndingAt(?\DateTimeInterface $endingAt): self
    {
        $this->endingAt = $endingAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getAuthor(): ?ExternaticConsultant
    {
        return $this->author;
    }

    public function setAuthor(?ExternaticConsultant $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, RecrutementProcess>
     */
    public function getRecrutementProcesses(): Collection
    {
        return $this->recrutementProcesses;
    }

    public function addRecrutementProcess(RecrutementProcess $recrutementProcess): self
    {
        if (!$this->recrutementProcesses->contains($recrutementProcess)) {
            $this->recrutementProcesses->add($recrutementProcess);
            $recrutementProcess->setAnnonce($this);
        }

        return $this;
    }

    public function removeRecrutementProcess(RecrutementProcess $recrutementProcess): self
    {
        if ($this->recrutementProcesses->removeElement($recrutementProcess)) {
            // set the owning side to null (unless already changed)
            if ($recrutementProcess->getAnnonce() === $this) {
                $recrutementProcess->setAnnonce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Candidat>
     */
    public function getFavorite(): Collection
    {
        return $this->favorite;
    }

    public function addFavorite(Candidat $favorite): self
    {
        if (!$this->favorite->contains($favorite)) {
            $this->favorite->add($favorite);
        }

        return $this;
    }

    public function removeFavorite(Candidat $favorite): self
    {
        $this->favorite->removeElement($favorite);

        return $this;
    }

    /**
     * @return Collection<int, Techno>
     */
    public function getTechno(): Collection
    {
        return $this->techno;
    }

    public function addTechno(Techno $techno): self
    {
        if (!$this->techno->contains($techno)) {
            $this->techno->add($techno);
        }

        return $this;
    }

    public function removeTechno(Techno $techno): self
    {
        $this->techno->removeElement($techno);

        return $this;
    }
}
