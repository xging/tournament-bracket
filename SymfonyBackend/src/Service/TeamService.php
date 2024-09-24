<?PHP
namespace App\Service;

use App\Service\Database\DatabaseServiceInterface;
use App\Service\FileLoader\FileLoaderServiceInterface;
use App\Service\Interfaces\TeamServiceInterface;
use App\Traits\LoggingTraits\LogInfoTrait;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel('TeamService')]
class TeamService implements TeamServiceInterface
{
    use LogInfoTrait;

    private DatabaseServiceInterface $databaseService;
    private FileLoaderServiceInterface $fileLoader;
    private LoggerInterface $logger;
    public function __construct(DatabaseServiceInterface $databaseService, FileLoaderServiceInterface $fileLoader, LoggerInterface $logger)
    {
        $this->databaseService = $databaseService;
        $this->fileLoader = $fileLoader;
        $this->logger = $logger;
    }

    public function loadAndAssignTeams(int $divisionNum, $teamList): array
    {

        $teams = $this->loadTeams($teamList);
        $groups = $this->groupTeams($teams, $divisionNum);
        $result = $this->saveTeamsToDatabase($groups);

        return $result;
    }

    private function groupTeams(array $teamNames, int $divisionNum): array
    {
        try {
            shuffle($teamNames);
            $teamSliceRange = $divisionNum * 8;
            $selectedTeams = array_slice($teamNames, 0, $teamSliceRange);
            $groups = array_chunk($selectedTeams, 8);
            $this->logger->info('The teams have been grouped:', ['GroupedTeams' => $groups]);
            return $groups;
        } catch (\Exception $e) {
            $this->logger->error(
                'Error occurred in groupTeams: ' . $e->getMessage(),
                ['exception' => $e]
            );
            return [];
        }
    }

    private function loadTeams($teamList): array
    {
        $teamNames = [];

        if($teamList == 'random') {
            for ($i = 0; $i <= 64; $i++) {
                $teamNames[] = [
                    'fullName' => 'Team-'.$i,
                    'shortName' => 'TM-'.$i,
                ];
            }
         $this->logger->info('The team data has been loaded:', ['Teams' => $teamNames]);
        } else if($teamList == 'list') {
            try {
                $data = $this->fileLoader->loadTeams();
                $dataCount = count($data);
    
                if (isset($data) && is_array($data)) {
                    foreach ($data as $team) {
                        if (isset($team['fullName']) && isset($team['shortName']) && $dataCount >= 8) {
                            $teamNames[] = [
                                'fullName' => $team['fullName'],
                                'shortName' => $team['shortName']
                            ];
                        } else {
                            $this->logger->error('The team data was not found:', ['team' => $team]);
                        }
                    }
                    $this->logger->info('The team data has been loaded:', ['Teams' => $teamNames]);
                } else {
                    $this->logger->error('The team data is not available or is not an array');
                }
            } catch (\Exception $e) {
                $this->logger->error(
                    'Error occurred while loading teams: ' . $e->getMessage(),
                    ['exception' => $e]
                );
            }
        } else {
            $teamNames = [];
            $this->logger->error('The team data is not available');
        }
        return $teamNames;
    }



    private function saveTeamsToDatabase(array $groups): array
    {
        $result = [];
        foreach ($groups as $index => $group) {
            $divisionId = $index + 1;

            $result[] = [
                'division_id' => $divisionId,
                'teams' => $group
            ];

            foreach ($group as $teamName) {
                $this->databaseService->addTeamMatch($divisionId, $teamName['fullName'], $teamName['shortName'], false, 0, false, false, false, false, false, false, '0');
            }

        }

        if (!empty($result)) {
            $this->logger->info('The teams have been saved into database', ['Result' => $result]);
        }

        return $result;
    }

}