<?PHP
namespace App\Service\Matches\DivisionMatches\AdditionalServices\ScoreGenerator;

use App\Service\Interfaces\ResultUpdateServiceInterface;
use App\Service\Database\DatabaseServiceInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel('customlog')]
class ResultUpdateService implements ResultUpdateServiceInterface
{
    private DatabaseServiceInterface $databaseService;
    private LoggerInterface $logger;

    public function __construct(DatabaseServiceInterface $databaseService, LoggerInterface $logger)
    {
        $this->databaseService = $databaseService;
        $this->logger = $logger;
    }

    public function updateResult(string $team): void
    {
        try {
            $result = $this->databaseService->getTMResultByShortname($team) + 1 * 10;
            $this->databaseService->setTMResultByShortname($team, $result);
            $this->logger->info("Winner Team result is updated. {team} : {res}",['team' => $team, 'res' => $result]);
        } catch (\Exception $e) {
            $this->logger->error('Error occurred while updating team result: ' . $e->getMessage());
        }
    }
}
