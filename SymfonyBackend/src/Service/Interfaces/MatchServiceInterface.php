<?PHP
namespace App\Service\Interfaces;

interface MatchServiceInterface
{
    public function createMatches(array $teams, int $numDiv, string $matchType, $divId): array;
    public function generateMatches(array $teamsA, int $numDiv, int $maxWins, array $match, array &$currentMatch, array $matchesList, $divId): array;
}