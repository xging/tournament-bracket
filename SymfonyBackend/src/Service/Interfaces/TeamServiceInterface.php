<?PHP
namespace App\Service\Interfaces;

interface TeamServiceInterface
{
    // public function loadAndAssignTeams(object $divisionA, object $divisionB): array;
    public function loadAndAssignTeams(int $divisionNum, string $teamList): array;
}