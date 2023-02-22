<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EventType $type = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Place $place = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $startedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $endedAt = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventParticipationRequest::class, orphanRemoval: true)]
    private Collection $participationRequests;

    #[ORM\Column(length: 255)]
    private ?string $qrCode = null;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventAttendance::class, orphanRemoval: true)]
    private Collection $attendances;

    public function __construct()
    {
        $this->participationRequests = new ArrayCollection();
        $this->attendances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?EventType
    {
        return $this->type;
    }

    public function setType(?EventType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeImmutable $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeImmutable $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    /**
     * @return Collection<int, EventParticipationRequest>
     */
    public function getParticipationRequests(): Collection
    {
        return $this->participationRequests;
    }

    public function addParticipationRequest(EventParticipationRequest $participationRequest): self
    {
        if (!$this->participationRequests->contains($participationRequest)) {
            $this->participationRequests->add($participationRequest);
            $participationRequest->setEvent($this);
        }

        return $this;
    }

    public function removeParticipationRequest(EventParticipationRequest $participationRequest): self
    {
        if ($this->participationRequests->removeElement($participationRequest)) {
            // set the owning side to null (unless already changed)
            if ($participationRequest->getEvent() === $this) {
                $participationRequest->setEvent(null);
            }
        }

        return $this;
    }

    public function getQrCode(): ?string
    {
        return $this->qrCode;
    }

    public function setQrCode(string $qrCode): self
    {
        $this->qrCode = $qrCode;

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
            $attendance->setEvent($this);
        }

        return $this;
    }

    public function removeAttendance(EventAttendance $attendance): self
    {
        if ($this->attendances->removeElement($attendance)) {
            // set the owning side to null (unless already changed)
            if ($attendance->getEvent() === $this) {
                $attendance->setEvent(null);
            }
        }

        return $this;
    }
}
