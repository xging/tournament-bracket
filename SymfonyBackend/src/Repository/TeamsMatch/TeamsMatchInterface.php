<?php

namespace App\Repository\TeamsMatch;

use App\Entity\TeamsMatch;

interface TeamsMatchInterface
{
    // public function find(mixed $id, ?int $lockMode = null, ?int $lockVersion = null): ?Teams;
    // public function findBy(array $criteria): array;
    // public function findAll(): array;
    public function pickedFlagCount(bool $flag): bool;
}
