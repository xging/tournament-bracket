<?PHP
namespace App\Service\Matches\PlayoffMatches;

interface PlayoffMatchesServiceInterface
{
    public function createPlayoffMatches(string $stage, array $availableStages): array;
    public function generatePlayoffMatch(int $numberOfParticipants, array $currentStageParticipants, array $possibleCurrentStageScores, string $currentStage): array;

}