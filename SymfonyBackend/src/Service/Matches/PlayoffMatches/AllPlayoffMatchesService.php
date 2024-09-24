<?php
namespace App\Service\Matches\PlayoffMatches;

class AllPlayoffMatchesService extends PlayoffMatchesService
{
    public function createPlayoffMatches(string $currentStage, array $availableStages): array
    {
        [$numberOfParticipants, $currentStageParticipants, $possibleCurrentStageScores, $availableStageIndex] = $this->preparePlayoffMatch($currentStage, $availableStages);
        [$winners, $losers, $matches] = $this->generatePlayoffMatch($numberOfParticipants, $currentStageParticipants, $possibleCurrentStageScores, $currentStage);

        $this->saveLogInfo("Winner List: {Teams}", ['Teams' => $winners]);
        $this->updateTeamPlayoffFlag($winners, $losers, $availableStageIndex);

        return [
            $currentStage => [
                'Matches' => $matches,
                'Winners' => $winners,
                'Losers' => $losers
            ]
        ];
    }

    public function generatePlayoffMatch(int $numberOfParticipants, array $currentStageParticipants, array $possibleCurrentStageScores, string $currentStage): array
    {
        shuffle($currentStageParticipants);
        [$matches, $winners, $losers] = [[], [], []];

        $initialNumberOfParticipants = $numberOfParticipants;

        [$byesTeamShortName] = $this->findByesTeams($numberOfParticipants, $currentStageParticipants);
        [$currentStageParticipants, $numberOfParticipants] = $this->findStageParticipantsTeams($currentStageParticipants, $byesTeamShortName);

        foreach ($byesTeamShortName as $name) {
            $winners[] = $name;
        }

        $this->saveLogInfo("Selected Byes teams: {Teams}", ['Teams' => $byesTeamShortName]);

        for ($i = 0; $i < $numberOfParticipants / 2; $i++) {
            $team1 = $currentStageParticipants[$i * 2];
            $team2 = $currentStageParticipants[$i * 2 + 1];

            $score = $this->getRandomMatchScore($possibleCurrentStageScores);
            list($score1, $score2) = explode(':', $score);

            $matches[] = $this->preparePlayoffMatchResult($team1, $team2, $score1, $score2);
            $result = $this->determineWinnerAndLoser((int) $score1, (int) $score2, $team1, $team2, $currentStage, $initialNumberOfParticipants);

            $winners[] = $result['winner'];
            $losers[] = $result['loser'];
        }


        return [$winners, $losers, $matches];
    }

}