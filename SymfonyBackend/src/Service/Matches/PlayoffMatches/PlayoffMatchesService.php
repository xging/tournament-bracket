<?PHP
namespace App\Service\Matches\PlayoffMatches;

use App\Entity\TeamsMatch;

use App\Repository\TeamsMatch\TeamsMatchRepository;
use App\Service\Database\DatabaseService;
use App\Traits\LoggingTraits\LogInfoTrait;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel('PlayoffService')]
abstract class PlayoffMatchesService implements PlayoffMatchesServiceInterface
{
    use LogInfoTrait;
    public DatabaseService $databaseService;
    public TeamsMatchRepository $teamsMatchRepository;
    public LoggerInterface $logger;

    public function __construct(
        DatabaseService $databaseService,
        TeamsMatchRepository $teamsMatchRepository,
        LoggerInterface $logger
    ) {
        $this->databaseService = $databaseService;
        $this->teamsMatchRepository = $teamsMatchRepository;
        $this->logger = $logger;
    }


    abstract public function createPlayoffMatches(string $currentStage, array $availableStages): array;
    abstract public function generatePlayoffMatch(int $numberOfParticipants, array $currentStageParticipants, array $possibleCurrentStageScores, string $currentStage): array;

    protected function preparePlayoffMatch(string $currentStage, array $availableStages): mixed
    {
        $stageNameFlag = strtolower($currentStage) . '_flag';
        $availableStageIndex = array_search($currentStage, $availableStages) + 1;

        $currentStageParticipants = $this->teamsMatchRepository->findBy([$stageNameFlag => true]);
        $numberOfParticipants = count($currentStageParticipants);
        shuffle($currentStageParticipants);

        $shortNames = array_map(function ($participant) {
            return $participant->getShortname();
        }, $currentStageParticipants);


        $this->saveLogInfo("Stage: {Stage} {Index}", ['Stage' => $currentStage, 'Index' => $availableStageIndex]);
        $this->saveLogInfo("{$currentStage} participants count: {count}", ['count' => $numberOfParticipants]);
        $this->saveLogInfo("Paricipants List: {Teams}", ['Teams' => $shortNames]);

        $scoreStage = $currentStage == 'grandfinal' ? 'grandfinal' : 'default';
        $possibleCurrentStageScores = PlayoffConfig::getPossibleScoresForStage($scoreStage);
        $this->saveLogInfo("possibleCurrentStageScores: {Stage}", ['Stage' => $possibleCurrentStageScores]);

        return [$numberOfParticipants, $currentStageParticipants, $possibleCurrentStageScores, $availableStageIndex];
    }

    protected function updateTeamPlayoffFlag(array $winners, array $losers, int $availableStageIndex): void
    {
        foreach ($winners as $name) {
            $this->databaseService->setPlayoffFlagByTeamShortName($name, $availableStageIndex + 1, true);
        }

        if ($availableStageIndex == 4) {
            foreach ($losers as $name) {
                $this->databaseService->setPlayoffFlagByTeamShortName($name, 6, true);
            }
        }
    }
    protected function findByesTeams($numberOfParticipants, $selectedWinners): mixed
    {
        $numberOfByes = $this->findNextPowerOfTwo($numberOfParticipants) - $numberOfParticipants;
        $selectedByes = array_slice($selectedWinners, 0, $numberOfByes);
        $byesTeamShortName = array_map(function ($participant) {
            return $participant->getShortname();
        }, $selectedByes);
        return [$byesTeamShortName];
    }

    protected function findStageParticipantsTeams($selectedWinners, $byesTeamShortName): mixed
    {
        $selectedWinners = array_filter($selectedWinners, function ($participant) use ($byesTeamShortName) {
            return !in_array($participant->getShortname(), $byesTeamShortName);
        });
        $selectedWinners = array_values($selectedWinners);
        return [$selectedWinners, count($selectedWinners)];
    }


    //This needs to calculate correct number of Byes teams.
    protected function findNextPowerOfTwo(int $number): int
    {
        $logNumber = log($number, 2);
        $roundedLogNumber = ceil($logNumber);
        $nextPowerOfTwo = pow(2, $roundedLogNumber);
        return $nextPowerOfTwo;
    }

    protected function getRandomMatchScore(array $possibleCurrentStageScores): string
    {
        return $possibleCurrentStageScores[array_rand($possibleCurrentStageScores)];
    }
    protected function preparePlayoffMatchResult(TeamsMatch $team1, TeamsMatch $team2, int $score1, int $score2): array
    {
        return [
            'Team1' => ['name' => $team1->getShortName(), 'score' => $score1],
            'Team2' => ['name' => $team2->getShortName(), 'score' => $score2]
        ];
    }

    protected function determineWinnerAndLoser(int $score1, int $score2, TeamsMatch $team1, TeamsMatch $team2, string $currentStage, int $numberOfParticipants): array
    {
        $winner = $score1 > $score2 ? $team1->getShortName() : $team2->getShortName();
        $loser = $score1 < $score2 ? $team1->getShortName() : $team2->getShortName();

        $points = PlayoffConfig::getPointsForStage($currentStage);

        $this->updateTeamResult($winner, $points);
        $this->updateTeamResult($loser, $points / 2);

        $this->saveLogInfo('Winner of the stage: {Winner}, Points: {Points}', ['Winner' => $winner, 'Points' => $points]);
        $this->saveLogInfo('Loser of the stage: {Loser}, Points: {Points}', ['Loser' => $winner, 'Points' => $points / 2]);

        $this->assignTeamPlacement($currentStage, $winner, $loser, $numberOfParticipants);

        return ['winner' => $winner, 'loser' => $loser];
    }


    protected function updateTeamResult(string $teamName, int $points): void
    {
        $currentResult = $this->databaseService->getMatchResult($teamName);
        $newResult = $currentResult + $points;
        $this->databaseService->setMatchResult($teamName, $newResult);
    }

    protected function assignTeamPlacement(string $currentStage, string $winner, string $loser, int $numberOfParticipants): void
    {
        $placement = PlayoffConfig::getStagePlacements($currentStage);
        $stages = PlayoffConfig::getStagesList();

        switch ($currentStage) {
            case $stages[5]:
            case $stages[4]:
                $this->databaseService->setPlaceByShortName($winner, $placement[0]);
                $this->databaseService->setPlaceByShortName($loser, $placement[1]);
                break;

            case $stages[2]:
                $this->databaseService->setPlaceByShortName($loser, $placement);
                break;

            case $stages[1]:
            case $stages[0]:
                $this->databaseService->setPlaceByShortName($loser, $placement . $numberOfParticipants);
                break;
        }
    }
}
