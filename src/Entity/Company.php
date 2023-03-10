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

    #[ORM\ManyToMany(targetEntity: ActivityArea::class, mappedBy: 'companies')]
    private Collection $activityAreas;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: IndividualProfile::class)]
    private Collection $collaborators;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    public function __construct()
    {
        $this->activityAreas = new ArrayCollection();
        $this->collaborators = new ArrayCollection();
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
            $collaborator->setCompany($this);
        }

        return $this;
    }

    public function removeCollaborator(IndividualProfile $collaborator): self
    {
        if ($this->collaborators->removeElement($collaborator)) {
            // set the owning side to null (unless already changed)
            if ($collaborator->getCompany() === $this) {
                $collaborator->setCompany(null);
            }
        }

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }
}
