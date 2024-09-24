<?PHP
namespace App\Tests\Service;

use App\Entity\TeamsMatch;
use PHPUnit\Framework\TestCase;
use App\Service\TeamService;
use App\Service\Database\DatabaseServiceInterface;
use App\Service\FileLoader\FileLoaderServiceInterface;
use Psr\Log\LoggerInterface;

class TeamServiceTest extends TestCase
{
    public function testLoadAndAssignTeamsSuccess()
    {
        $mockDatabaseService = $this->createMock(DatabaseServiceInterface::class);
        $mockFileLoader = $this->createMock(FileLoaderServiceInterface::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $mockFileLoader->method('loadTeams')
            ->willReturn([
                ['fullName' => 'Team A', 'shortName' => 'A'],
                ['fullName' => 'Team B', 'shortName' => 'B'],
                ['fullName' => 'Team C', 'shortName' => 'C'],
                ['fullName' => 'Team D', 'shortName' => 'D'],
                ['fullName' => 'Team E', 'shortName' => 'E'],
                ['fullName' => 'Team F', 'shortName' => 'F'],
                ['fullName' => 'Team G', 'shortName' => 'G'],
                ['fullName' => 'Team H', 'shortName' => 'H']
            ]);

        $teamMatchMock = $this->createMock(TeamsMatch::class);

        $mockDatabaseService->expects($this->exactly(8))
            ->method('addTeamMatch')
            ->willReturn($teamMatchMock);

        $service = new TeamService($mockDatabaseService, $mockFileLoader, $mockLogger);

        $result = $service->loadAndAssignTeams(1, 'random');

        // var_dump($result);

        $this->assertNotEmpty($result, 'loadAndAssignTeams is empty');

        $resultTeams = array_merge(...array_column($result, 'teams'));
        $expectedTeams = [
            ['fullName' => 'Team A', 'shortName' => 'A'],
            ['fullName' => 'Team B', 'shortName' => 'B'],
            ['fullName' => 'Team C', 'shortName' => 'C'],
            ['fullName' => 'Team D', 'shortName' => 'D'],
            ['fullName' => 'Team E', 'shortName' => 'E'],
            ['fullName' => 'Team F', 'shortName' => 'F'],
            ['fullName' => 'Team G', 'shortName' => 'G'],
            ['fullName' => 'Team H', 'shortName' => 'H']
        ];

        $this->assertEqualsCanonicalizing($expectedTeams, $resultTeams, 'loadAndAssignTeams is wrong');
    }

}
