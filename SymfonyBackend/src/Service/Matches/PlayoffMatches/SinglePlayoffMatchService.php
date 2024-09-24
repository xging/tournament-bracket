<?php
namespace App\Service\Matches\PlayoffMatches;

class SinglePlayoffMatchService extends PlayoffMatchesService
{
    //TODO: Write logic for single matches 
    public function createPlayoffMatches(string $currentStage, array $availableStages): array
    {
    return [];
    }
    

    public function generatePlayoffMatch(int $numberOfParticipants, array $currentStageParticipants, array $possibleCurrentStageScores, string $currentStage): array
    {
    return [];
    }
}