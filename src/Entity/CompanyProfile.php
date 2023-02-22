<?php

namespace App\Entity;

use App\Repository\CompanyProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyProfileRepository::class)]
class CompanyProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'companyProfile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Company::class, orphanRemoval: true)]
    private Collection $companies;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'representedCompany', targetEntity: EventAttendance::class)]
    private Collection $attendances;

    #[ORM\OneToMany(mappedBy: 'requestedBy', targetEntity: JoinCompanyRequest::class, orphanRemoval: true)]
    private Collection $sentJoinCompanyRequests;

    #[ORM\OneToMany(mappedBy: 'requestedTo', targetEntity: JoinCompanyRequest::class, orphanRemoval: true)]
    private Collection $receivedJoinCompanyRequests;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->attendances = new ArrayCollection();
        $this->sentJoinCompanyRequests = new ArrayCollection();
        $this->receivedJoinCompanyRequests = new ArrayCollection();
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

    /**
     * @return Collection<int, Company>
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies->add($company);
            $company->setOwner($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getOwner() === $this) {
                $company->setOwner(null);
            }
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
     * @return Collection<int, EventAttendance>
     */
    public function getAttendances(): Collection
    {
        return $this->attendances;
    }

    public function addAttendance(EventAttendance $attendance): self
    {
        if (!$this->attendances->contains($attendance)) {
            $this->attendances->add($attendance);
            $attendance->setRepresentedCompany($this);
        }

        return $this;
    }

    public function removeAttendance(EventAttendance $attendance): self
    {
        if ($this->attendances->removeElement($attendance)) {
            // set the owning side to null (unless already changed)
            if ($attendance->getRepresentedCompany() === $this) {
                $attendance->setRepresentedCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JoinCompanyRequest>
     */
    public function getSentJoinCompanyRequests(): Collection
    {
        return $this->sentJoinCompanyRequests;
    }

    public function addSentJoinCompanyRequest(JoinCompanyRequest $sentJoinCompanyRequest): self
    {
        if (!$this->sentJoinCompanyRequest->contains($sentJoinCompanyRequest)) {
            $this->sentJoinCompanyRequest->add($sentJoinCompanyRequest);
            $sentJoinCompanyRequest->setRequestedBy($this);
        }

        return $this;
    }

    public function removeSentJoinCompanyRequest(JoinCompanyRequest $sentJoinCompanyRequest): self
    {
        if ($this->sentJoinCompanyRequest->removeElement($sentJoinCompanyRequest)) {
            // set the owning side to null (unless already changed)
            if ($sentJoinCompanyRequest->getRequestedBy() === $this) {
                $sentJoinCompanyRequest->setRequestedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JoinCompanyRequest>
     */
    public function getReceivedJoinCompanyRequests(): Collection
    {
        return $this->receivedJoinCompanyRequests;
    }

    public function addReceivedJoinCompanyRequest(JoinCompanyRequest $receivedJoinCompanyRequest): self
    {
        if (!$this->receivedJoinCompanyRequests->contains($receivedJoinCompanyRequest)) {
            $this->receivedJoinCompanyRequests->add($receivedJoinCompanyRequest);
            $receivedJoinCompanyRequest->setRequestedTo($this);
        }

        return $this;
    }

    public function removeReceivedJoinCompanyRequest(JoinCompanyRequest $receivedJoinCompanyRequest): self
    {
        if ($this->receivedJoinCompanyRequests->removeElement($receivedJoinCompanyRequest)) {
            // set the owning side to null (unless already changed)
            if ($receivedJoinCompanyRequest->getRequestedTo() === $this) {
                $receivedJoinCompanyRequest->setRequestedTo(null);
            }
        }

        return $this;
    }
}
