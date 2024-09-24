<?PHP
namespace App\Service\Interfaces;

interface DivisionServiceInterface
{
    public function createDivisions(int $numOfDivisions): array;
    // public function getDivisions(): array;
}