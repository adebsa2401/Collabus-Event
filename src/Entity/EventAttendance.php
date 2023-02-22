<?php

namespace App\Entity;

use App\Repository\EventAttendanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventAttendanceRepository::class)]
class EventAttendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eventAttendances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?IndividualProfile $participant = null;

    #[ORM\ManyToOne(inversedBy: 'attendances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isVerified = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $isVerifiedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParticipant(): ?IndividualProfile
    {
        return $this->participant;
    }

    public function setParticipant(?IndividualProfile $participant): self
    {
        $this->participant = $participant;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getIsVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->isVerifiedAt;
    }

    public function setIsVerifiedAt(?\DateTimeImmutable $isVerifiedAt): self
    {
        $this->isVerifiedAt = $isVerifiedAt;

        return $this;
    }
}
