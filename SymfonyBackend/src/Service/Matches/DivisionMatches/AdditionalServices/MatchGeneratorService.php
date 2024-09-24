<?php
namespace App\Service\Matches\DivisionMatches\AdditionalServices;

use App\Service\Interfaces\MatchGeneratorServiceInterface;
use App\Service\Database\DatabaseServiceInterface;
use App\Service\Interfaces\RandomScoreServiceInterface;
use App\Service\Interfaces\ResultUpdateServiceInterface;
use Psr\Log\LoggerInterface;

class MatchGeneratorService implements MatchGeneratorServiceInterface
{
    private DatabaseServiceInterface $databaseService;
    private LoggerInterface $logger;
    private RandomScoreServiceInterface $scoreGenerator;
    private ResultUpdateServiceInterface $resultUpdater;

    public function __construct(
        DatabaseServiceInterface $databaseService,
        LoggerInterface $logger,
        RandomScoreServiceInterface $scoreGenerator,
        ResultUpdateServiceInterface $resultUpdater
    ) {
        $this->databaseService = $databaseService;
        $this->logger = $logger;
        $this->scoreGenerator = $scoreGenerator;
        $this->resultUpdater = $resultUpdater;
    }

    public function generateMatchScore(array $team1, array $team2, array $winnerTeams, array &$wins, int $maxWins): string
    {
        $score = $this->scoreGenerator->generateScore($team1, $team2, $winnerTeams, $wins, $maxWins);
        $this->applyScore($score, $team1['shortName'], $team2['shortName'], $wins);

        return $score;
    }

    private function applyScore(string $score, string $team1, string $team2, array &$wins): void
    {
        if (in_array(needle: $score, haystack: ['2:0', '2:1'])) {
            $this->incrementWins($team1, $wins);
        } else {
            $this->incrementWins($team2, $wins);
        }
    }

    private function incrementWins(string $team, array &$wins): void
    {
        $wins[$team]++;
        $this->resultUpdater->updateResult($team);
    }
}
