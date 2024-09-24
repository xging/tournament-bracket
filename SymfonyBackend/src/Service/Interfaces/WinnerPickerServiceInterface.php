<?PHP
namespace App\Service\Interfaces;

interface WinnerPickerServiceInterface
{
    public function pickWinners(array $teams, int $divisionId, int $divisionCount): array;
}