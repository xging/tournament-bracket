<?php
namespace App\Service\Matches\DivisionMatches;

use App\Service\Interfaces\MatchServiceInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use App\Service\Database\DatabaseServiceInterface;
use App\Service\Matches\DivisionMatches\AdditionalServices\MatchGeneratorService;
use App\Service\Matches\DivisionMatches\AdditionalServices\WinnerPickerService;
use App\Service\Cache\CacheService;



#[WithMonologChannel('AllMatchesService')]
abstract class MatchService implements MatchServiceInterface
{
    public DatabaseServiceInterface $databaseService;
    public LoggerInterface $logger;
    public MatchGeneratorService $matchGenerator;
    public WinnerPickerService $winnerPicker;
    public CacheService $cacheService;

    public function __construct(
        DatabaseServiceInterface $databaseService,
        LoggerInterface $logger,
        MatchGeneratorService $matchGenerator,
        WinnerPickerService $winnerPicker,
        CacheService $cacheService
    ) {
        $this->databaseService = $databaseService;
        $this->logger = $logger;
        $this->matchGenerator = $matchGenerator;
        $this->winnerPicker = $winnerPicker;
        $this->cacheService = $cacheService;
    }

    abstract public function createMatches(array $teams, int $numDiv, string $matchType, $divId): array;
    
    abstract public function generateMatches(array $teamsA, int $numDiv, int $maxWins, array $match, array &$currentMatch, array $matchesList, $divId): array;

    protected function checkMatchExists(array $matchesList, array $currentMatch): bool
    {
        $matchFound = false;
        foreach ($matchesList as $matches) {
            if ($matches === $currentMatch || $matches === array_reverse($currentMatch)) {
                $matchFound = true;
                break;
            }
        }

        if ($matchFound) {
            $this->logger->info("Current Match is found {Match}", ['Match' => $currentMatch]);
        } else {
            $this->logger->info("Current Match is not found {Match}", ['Match' => $currentMatch]);
        }

        return $matchFound;
    }
}
