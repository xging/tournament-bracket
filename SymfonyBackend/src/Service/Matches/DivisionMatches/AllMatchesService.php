<?php
namespace App\Service\Matches\DivisionMatches;

class AllMatchesService extends MatchService
{
    public function createMatches(array $teams, int $numDiv, string $matchType , $divId): array {
        $maxWins = 7;
        $currentMatch =[];
        $matchesList = $this->generateMatches($teams, $numDiv, $maxWins,[],$currentMatch,[],null);
        return $matchesList;
    }

    public function generateMatches(array $teams, int $numDiv, int $maxWins, array $match, array &$currentMatch, array $matchesList, $divId): array
    {
        $matches = [];
        foreach ($teams as $division) {
            $divisionName = $this->databaseService->getDivisionName($division['division_id']);
            $this->logger->info("Division ID: {$division['division_id']}", ['Name' => $divisionName]);

            $winnerTeams = $this->winnerPicker->pickWinners($division['teams'], $division['division_id'], $numDiv);

            foreach ($division['teams'] as $i => $team) {
                $teamWinScore = array_fill_keys(array_column($division['teams'], 'shortName'), 0);
                for ($j = $i + 1; $j < count($division['teams']); $j++) {
                    $team2 = $division['teams'][$j];

                    $score = $this->matchGenerator->generateMatchScore($team, $team2, $winnerTeams, $teamWinScore, $maxWins);

                    $matches[$division['division_id']]['Meetings'][$team['shortName']][$team2['shortName']] = $score;
                    $matches[$division['division_id']]['Meetings'][$team2['shortName']][$team['shortName']] = implode(':', array_reverse(explode(':', $score)));

                    $this->databaseService->addMatchesHist($team['shortName'], $team2['shortName'], $score);
                }
                $this->logger->info("Team {$i} Name: {Team}", ['Team' => $team['shortName'], 'Matches' => $teamWinScore]);
            }
            $matches[$division['division_id']]['additional_info']['picked_winners'] = $winnerTeams;
        }

        $placesCountLast = $numDiv * 8;
        $placesCountFirst = ceil(($placesCountLast + 1) / 2);
        $place = $placesCountFirst . '-' . $placesCountLast;

        $loserTeams = $this->databaseService->getTeamByPickedFlag(false);
        $loserTeamsName = array_map(function ($participant) {
            return $participant->getShortname();
        }, $loserTeams);

        foreach ($loserTeams as $name) {
            $this->databaseService->setPlaceByShortName($name->getShortname(), $place);
        }

        $this->logger->info("Loser Teams Name: {Team}", ['Team' => $loserTeamsName]);

        return [$matches];
    }
}