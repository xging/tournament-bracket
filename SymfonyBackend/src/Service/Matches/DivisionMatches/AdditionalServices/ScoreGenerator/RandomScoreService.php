<?PHP
namespace App\Service\Matches\DivisionMatches\AdditionalServices\ScoreGenerator;

use App\Service\Interfaces\RandomScoreServiceInterface;

class RandomScoreService implements RandomScoreServiceInterface
{
    public function generateScore(array $team1, array $team2, array $winnerTeams, array &$wins, int $maxWins): string
    {
        $possibleScores = ['2:0', '2:1', '1:2', '0:2'];

        if ($this->canWin($team1['shortName'], $winnerTeams, $wins, $maxWins)) {
            return $possibleScores[mt_rand(0, 1)];
        } elseif ($this->canWin($team2['shortName'], $winnerTeams, $wins, $maxWins)) {
            return $possibleScores[mt_rand(2, 3)];
        }

        return $possibleScores[mt_rand(0, 3)];
    }

    private function canWin(string $team, array $winnerTeams, array $wins, int $maxWins): bool
    {
        return in_array($team, $winnerTeams) && $wins[$team] < $maxWins;
    }
}
