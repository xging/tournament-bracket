<?php

namespace App\Entity;

use App\Repository\TeamsMatch\TeamsMatchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamsMatchRepository::class)]
class TeamsMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $division_id = null;

    #[ORM\Column(length: 255)]
    private ?string $fullname = null;

    #[ORM\Column(length: 255)]
    private ?string $shortname = null;

    #[ORM\Column]
    private ?bool $picked_flag = null;

    #[ORM\Column]
    private ?int $result = null;

    #[ORM\Column(nullable: true)]
    private ?bool $quarterfinal_flag = null;

    #[ORM\Column(nullable: true)]
    private ?bool $semifinal_flag = null;

    #[ORM\Column(nullable: true)]
    private ?bool $bronzemedal_flag = null;

    #[ORM\Column(nullable: true)]
    private ?bool $grandfinal_flag = null;

    #[ORM\Column(nullable: true)]
    private ?bool $round_1_flag = null;

    #[ORM\Column(nullable: true)]
    private ?bool $round_2_flag = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $place = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDivisionId(): ?int
    {
        return $this->division_id;
    }

    public function setDivisionId(int $division_id): static
    {
        $this->division_id = $division_id;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): static
    {
        $this->shortname = $shortname;

        return $this;
    }

    public function isPickedFlag(): ?bool
    {
        return $this->picked_flag;
    }

    public function setPickedFlag(bool $picked_flag): static
    {
        $this->picked_flag = $picked_flag;

        return $this;
    }

    public function getResult(): ?int
    {
        return $this->result;
    }

    public function setResult(int $result): static
    {
        $this->result = $result;

        return $this;
    }

    public function isQuarterfinalFlag(): ?bool
    {
        return $this->quarterfinal_flag;
    }

    public function setQuarterfinalFlag(?bool $quarterfinal_flag): static
    {
        $this->quarterfinal_flag = $quarterfinal_flag;

        return $this;
    }

    public function isSemifinalFlag(): ?bool
    {
        return $this->semifinal_flag;
    }

    public function setSemifinalFlag(?bool $semifinal_flag): static
    {
        $this->semifinal_flag = $semifinal_flag;

        return $this;
    }

    public function isBronzemedalFlag(): ?bool
    {
        return $this->bronzemedal_flag;
    }

    public function setBronzemedalFlag(?bool $bronzemedal_flag): static
    {
        $this->bronzemedal_flag = $bronzemedal_flag;

        return $this;
    }

    public function isGrandfinalFlag(): ?bool
    {
        return $this->grandfinal_flag;
    }

    public function setGrandfinalFlag(?bool $grandfinal_flag): static
    {
        $this->grandfinal_flag = $grandfinal_flag;

        return $this;
    }

    public function isRound1Flag(): ?bool
    {
        return $this->round_1_flag;
    }

    public function setRound1Flag(?bool $round_1_flag): static
    {
        $this->round_1_flag = $round_1_flag;

        return $this;
    }

    public function isRound2Flag(): ?bool
    {
        return $this->round_2_flag;
    }

    public function setRound2Flag(?bool $round_2_flag): static
    {
        $this->round_2_flag = $round_2_flag;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): static
    {
        $this->place = $place;

        return $this;
    }
}
