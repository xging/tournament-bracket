<?php
namespace App\Service\Matches\DivisionMatches;
use Monolog\Attribute\WithMonologChannel;
#[WithMonologChannel('AllMatchesService')]
class SingleMatchService extends MatchService
{
    public function createMatches(array $teams, int $numDiv, string $matchType, $divId): array
    {

        $matches = [];
        $maxWins = 7;
        $currentMatch = [];

        $matchesList = $this->cacheService->cacheArray(key: 'matchesListArray');
        $matchesList = $this->generateMatches($teams, $numDiv, $maxWins, $matches, $currentMatch, $matchesList, $divId);

        $this->cacheService->cacheArray($currentMatch, 'save', 'matchesListArray');

        return $matchesList;
    }

    public function generateMatches(array $teamsA, int $numDiv, int $maxWins, array $match, array &$currentMatch, array $matchesList, $divId): array
    {

        $divisionId = (int) $divId;
        $filteredDivision = array_filter($teamsA, function ($division) use ($divisionId) {
            return $division['division_id'] === $divisionId;
        });
        $winnerTeams = [];


        foreach ($filteredDivision as $division) {

            $divisionName = $this->databaseService->getDivisionName($division['division_id']);
            $this->logger->info("Division ID: {$division['division_id']}", ['Name' => $divisionName]);

            $cacheKey = 'winnersListArray' . $division['division_id'];
            $winnerTeams = $this->cacheService->cacheArray(key: $cacheKey);

            if (empty($winnerTeams)) {
                $winnerTeams = $this->winnerPicker->pickWinners($division['teams'], $division['division_id'], $numDiv);
            }
            $winnerTeams = $this->cacheService->cacheArray($winnerTeams, 'save', $cacheKey);

            $newMatch = [];

            foreach ($division['teams'] as $i => $team) {
                $opponentFound = false;
                $teamWinScore = array_fill_keys(array_column($division['teams'], 'shortName'), 0);

                for ($j = $i + 1; $j <= count($division['teams']); $j++) {

                    if ($j > 7) {
                        break;
                    }

                    $team2 = $division['teams'][$j];
                    $currentMatch = [$team['shortName'], $team2['shortName']];

                    if ($this->checkMatchExists($matchesList, $currentMatch)) {
                        $this->logger->info("Current Match is found {Match} SKIP", ['Match' => $currentMatch]);
                        continue;
                    }

                    $score = $this->matchGenerator->generateMatchScore($team, $team2, $winnerTeams, $teamWinScore, $maxWins);


                     //TODO:Need to fix matches output logic
                    $newMatch[$division['division_id']]['Matches'][$team['shortName']][$team2['shortName']] = $score;
                    $newMatch[$division['division_id']]['Matches'][$team2['shortName']][$team['shortName']] = implode(':', array_reverse(explode(':', $score)));


                   
            
                    
                    $this->cacheService->cacheArray($newMatch, 'save', 'newMatches');

                    $this->databaseService->addMatchesHist($team['shortName'], $team2['shortName'], $score);
                    $opponentFound = true;
                    break;
                }

                $this->logger->info("Team {$i} Name: {Team}", ['Team' => $team['shortName'], 'Matches' => $teamWinScore]);

                if (!$opponentFound) {
                    continue;
                } else {
                    break;
                }
            }
        }
        $match[] = $newMatch;

        return $match;
    }
}