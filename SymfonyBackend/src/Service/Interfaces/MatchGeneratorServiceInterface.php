<?PHP
namespace App\Service\Interfaces;

interface MatchGeneratorServiceInterface
{
    public function generateMatchScore(array $team1, array $team2, array $winnerTeams, array &$wins, int $maxWins): string;
}