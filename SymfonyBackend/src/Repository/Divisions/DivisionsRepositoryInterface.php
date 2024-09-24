<?php

namespace App\Repository\Divisions;

use App\Entity\Divisions;
use App\Entity\Teams;

interface DivisionsRepositoryInterface
{
    public function findAll(): array;
}