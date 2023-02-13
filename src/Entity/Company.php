<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'companies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CompanyProfile $owner = null;

    #[ORM\ManyToMany(targetEntity: IndividualProfile::class, inversedBy: 'collaborationCompanies')]
    private Collection $collaborators;

    #[ORM\ManyToMany(targetEntity: ActivityArea::class, mappedBy: 'companies')]
    private Collection $activityAreas;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->collaborators = new ArrayCollection();
        $this->activityAreas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?CompanyProfile
    {
        return $this->owner;
    }

    public function setOwner(?CompanyProfile $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, IndividualProfile>
     */
    public function getCollaborators(): Collection
    {
        return $this->collaborators;
    }

    public function addCollaborator(IndividualProfile $collaborator): self
    {
        if (!$this->collaborators->contains($collaborator)) {
            $this->collaborators->add($collaborator);
        }

        return $this;
    }

    public function removeCollaborator(IndividualProfile $collaborator): self
    {
        $this->collaborators->removeElement($collaborator);

        return $this;
    }

    /**
     * @return Collection<int, ActivityArea>
     */
    public function getActivityAreas(): Collection
    {
        return $this->activityAreas;
    }

    public function addActivityArea(ActivityArea $activityArea): self
    {
        if (!$this->activityAreas->contains($activityArea)) {
            $this->activityAreas->add($activityArea);
            $activityArea->addCompany($this);
        }

        return $this;
    }

    public function removeActivityArea(ActivityArea $activityArea): self
    {
        if ($this->activityAreas->removeElement($activityArea)) {
            $activityArea->removeCompany($this);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
