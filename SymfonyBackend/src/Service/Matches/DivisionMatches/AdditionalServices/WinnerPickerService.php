<?php
namespace App\Service\Matches\DivisionMatches\AdditionalServices;

use App\Repository\TeamsMatch\TeamsMatchInterface;
use App\Service\Interfaces\WinnerPickerServiceInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use App\Service\Database\DatabaseServiceInterface;

#[WithMonologChannel('Custom2')]
class WinnerPickerService implements WinnerPickerServiceInterface
{
    private TeamsMatchInterface $teamsMatchRepository;
    private DatabaseServiceInterface $databaseService;
    private LoggerInterface $logger;

    public function __construct(
        TeamsMatchInterface $teamsMatchRepository,
        DatabaseServiceInterface $databaseService,
        LoggerInterface $logger
    ) {
        $this->teamsMatchRepository = $teamsMatchRepository;
        $this->databaseService = $databaseService;
        $this->logger = $logger;
    }

    public function pickWinners(array $teams, int $divisionId, int $divisionCount): array
    {

        $numWinners = 4;

        try {
            $pickedFlag = $this->teamsMatchRepository->pickedFlagCount(true);
            $this->logger->info('Picked flag count: ' . $pickedFlag);
        } catch (\Exception $e) {
            $this->logger->error('Error occurred in pickWinners: ' . $e->getMessage());
            return [];
        }


        try {
            $pickedWinners = $this->databaseService->getShortNameByPickedFlag(true, $divisionId);
            $this->logger->info('Picked Winners: ' . json_encode($pickedWinners));
        } catch (\Exception $e) {
            $this->logger->error('Error occurred in pickWinners: ' . $e->getMessage());
            return [];
        }

        if (!$pickedFlag || empty($pickedWinners)) {

            $winnerTeams = array_rand(array_flip(array_column($teams, 'shortName')), $numWinners);

            $winnerTeamsCount = count($winnerTeams) * $divisionCount;
            $this->logger->info('Count {Teams}', ['Teams' => $winnerTeamsCount]);
            foreach ($winnerTeams as $winner) {

                try {
                    $this->databaseService->setPickedFlagByTeamShortName($winner, true);
                    $this->logger->info('Winner Pick flag is set.');

                    if($winnerTeamsCount <=16 && $winnerTeamsCount >8) {
                        $this->databaseService->setPlayoffFlagByTeamShortName($winner,'2',true);
                        $this->logger->info('Team meet the criteria to participate in the round 2.',['Team' => $winner]);
                    } else if ($winnerTeamsCount <=8 && $winnerTeamsCount>4){
                        $this->databaseService->setPlayoffFlagByTeamShortName($winner,'3',true);
                        $this->logger->info('Team meet the criteria to participate in the quarter-finals.',['Team' => $winner]);
                    } else if ($winnerTeamsCount <=4){
                        $this->databaseService->setPlayoffFlagByTeamShortName($winner,'4',true);
                        $this->logger->info('Team meet the criteria to participate in the semi-finals.',['Team' => $winner]);
                    } else {
                        $this->databaseService->setPlayoffFlagByTeamShortName($winner,'1',true);
                        $this->logger->info('Team meet the criteria to participate in the round 1.',['Team' => $winner]);
                    }
                    
                } catch (\Exception $e) {
                    $this->logger->error('Error occurred in pickWinners: ' . $e->getMessage());
                    return [];
                }
            }

        } else {
            $winnerTeams = $pickedWinners;
        }

        return $winnerTeams;
    }
}
