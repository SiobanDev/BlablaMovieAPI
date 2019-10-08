<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VoteRepository")
 */
class Vote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="votations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $voter;

    /**
     * @ORM\Column(type="datetime")
     */
    private $votation_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $movie_id;

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
        return $this->votation_date;
    }

    public function setVotationDate(\DateTimeInterface $votation_date): self
    {
        $this->votation_date = $votation_date;

        return $this;
    }

    public function getMovieId(): ?string
    {
        return $this->movie_id;
    }

    public function setMovieId(string $movie_id): self
    {
        $this->movie_id = $movie_id;

        return $this;
    }
}
