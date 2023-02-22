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

    #[ORM\ManyToOne(inversedBy: 'collaborators')]
    private ?Company $company = null;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: EventParticipationRequest::class, orphanRemoval: true)]
    private Collection $eventParticipationRequests;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: EventAttendance::class, orphanRemoval: true)]
    private Collection $eventAttendances;

    public function __construct()
    {
        $this->eventParticipationRequests = new ArrayCollection();
        $this->eventAttendances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection<int, EventParticipationRequest>
     */
    public function getEventParticipationRequests(): Collection
    {
        return $this->eventParticipationRequests;
    }

    public function addEventParticipationRequest(EventParticipationRequest $eventParticipationRequest): self
    {
        if (!$this->eventParticipationRequests->contains($eventParticipationRequest)) {
            $this->eventParticipationRequests->add($eventParticipationRequest);
            $eventParticipationRequest->setParticipant($this);
        }

        return $this;
    }

    public function removeEventParticipationRequest(EventParticipationRequest $eventParticipationRequest): self
    {
        if ($this->eventParticipationRequests->removeElement($eventParticipationRequest)) {
            // set the owning side to null (unless already changed)
            if ($eventParticipationRequest->getParticipant() === $this) {
                $eventParticipationRequest->setParticipant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EventAttendance>
     */
    public function getEventAttendances(): Collection
    {
        return $this->eventAttendances;
    }

    public function addEventAttendance(EventAttendance $eventAttendance): self
    {
        if (!$this->eventAttendances->contains($eventAttendance)) {
            $this->eventAttendances->add($eventAttendance);
            $eventAttendance->setParticipant($this);
        }

        return $this;
    }

    public function removeEventAttendance(EventAttendance $eventAttendance): self
    {
        if ($this->eventAttendances->removeElement($eventAttendance)) {
            // set the owning side to null (unless already changed)
            if ($eventAttendance->getParticipant() === $this) {
                $eventAttendance->setParticipant(null);
            }
        }

        return $this;
    }
}
