<?PHP
namespace App\Service\Interfaces;

interface RandomScoreServiceInterface
{
    public function generateScore(array $team1, array $team2, array $winnerTeams, array &$wins, int $maxWins): string;
}