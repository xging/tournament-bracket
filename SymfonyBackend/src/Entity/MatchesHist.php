<?php

namespace App\Entity;

use App\Repository\MatchesHistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchesHistRepository::class)]
class MatchesHist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $team_1 = null;

    #[ORM\Column(length: 255)]
    private ?string $team_2 = null;

    #[ORM\Column(length: 255)]
    private ?string $result = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeam1(): ?string
    {
        return $this->team_1;
    }

    public function setTeam1(string $team_1): static
    {
        $this->team_1 = $team_1;

        return $this;
    }

    public function getTeam2(): ?string
    {
        return $this->team_2;
    }

    public function setTeam2(string $team_2): static
    {
        $this->team_2 = $team_2;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): static
    {
        $this->result = $result;

        return $this;
    }
}
