<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    const GROUP_SELF = "Vote::self";
    const GROUP_VOTER = "Vote::voter";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="votations")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({Vote::GROUP_VOTER})
     */
    private $voter;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     * @Groups({Vote::GROUP_SELF})
     */
    private $votationDate;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Groups({Vote::GROUP_SELF})
     */
    private $movieId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVoter(): ?User
    {
        return $this->voter;
    }

    public function setVoter(?User $voter): self
    {
        $this->voter = $voter;

        return $this;
    }

    public function getVotationDate(): ?\DateTimeInterface
    {
        return $this->votationDate;
    }

    public function setVotationDate(\DateTimeInterface $votationDate): self
    {
        $this->votationDate = $votationDate;

        return $this;
    }

    public function getMovieId(): ?string
    {
        return $this->movieId;
    }

    public function setMovieId(string $movieId): self
    {
        $this->movieId = $movieId;

        return $this;
    }
}
