<?php

namespace App\Entity;

use App\Repository\IndividualProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IndividualProfileRepository::class)]
class IndividualProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'individualProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\ManyToMany(targetEntity: Company::class, mappedBy: 'collaborators')]
    private Collection $collaborationCompanies;

    public function __construct()
    {
        $this->collaborationCompanies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCollaborationCompanies(): Collection
    {
        return $this->collaborationCompanies;
    }

    public function addCollaborationCompany(Company $collaborationCompany): self
    {
        if (!$this->collaborationCompanies->contains($collaborationCompany)) {
            $this->collaborationCompanies->add($collaborationCompany);
            $collaborationCompany->addCollaborator($this);
        }

        return $this;
    }

    public function removeCollaborationCompany(Company $collaborationCompany): self
    {
        if ($this->collaborationCompanies->removeElement($collaborationCompany)) {
            $collaborationCompany->removeCollaborator($this);
        }

        return $this;
    }
}
