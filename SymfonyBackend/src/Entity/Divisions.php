<?php

namespace App\Entity;

use App\Repository\DivisionsRepository;
use Doctrine\ORM\Mapping as ORM;

// #[ORM\Entity(repositoryClass: DivisionsRepository::class)]
/**
 * @ORM\Entity(repositoryClass="App\CustomRepository\DivisionsRepository")
 */

#[ORM\Entity(repositoryClass: "App\Repository\Divisions\DivisionsRepository")]
class Divisions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $name = null;

    #[ORM\Column]
    private ?int $division_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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
}
