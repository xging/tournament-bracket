<?PHP
namespace App\Controller\Api;

use App\Service\DivisionService;
use App\Service\Matches\DivisionMatches\MatchConfig;
use App\Service\Matches\DivisionMatches\SingleMatchService;
use App\Service\Matches\DivisionMatches\AllMatchesService;
use App\Service\TeamService;
use App\Service\Database\DatabaseService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\Cache\CacheService;



#[WithMonologChannel('GenerateMatchController')]
class GenerateMatchController extends AbstractController
{
    private DivisionService $divisionService;
    private TeamService $teamService;
    private DatabaseService $databaseService;
    private LoggerInterface $logger;
    private CacheService $cacheService;
    private ValidatorInterface $validator;
    private AllMatchesService $allMatchesService;
    private SingleMatchService $singleMatchService;


    public function __construct(
        DivisionService $divisionService,
        TeamService $teamService,
        DatabaseService $databaseService,
        LoggerInterface $logger,
        CacheService $cacheService,
        ValidatorInterface $validator,
        AllMatchesService $allMatchesService,
        SingleMatchService $singleMatchService
    ) {
        $this->divisionService = $divisionService;
        $this->teamService = $teamService;
        $this->databaseService = $databaseService;
        $this->logger = $logger;
        $this->cacheService = $cacheService;
        $this->validator = $validator;
        $this->allMatchesService = $allMatchesService;
        $this->singleMatchService = $singleMatchService;
    }


    #[Route('/api/generate-match-data/{numberOfDivisions}/{teamType}/{matchType}/{divisionId}',
        name: 'generate-match-data',
        defaults: ['numberOfDivisions' => 2, 'teamType' => 'random', 'matchType' => 'all', 'divisionId' => 'all']
    )]
    public function generateDivisionMatches($numberOfDivisions, $teamType, $matchType, $divisionId): JsonResponse
    {
        $numberOfDivisions = (int) $numberOfDivisions;
        $teamType = (string) $teamType;
        $matchType = (string) $matchType;

        $errors = $this->validateParams($numberOfDivisions, $teamType, $matchType, $divisionId);
        if (!empty($errors)) {
            return new JsonResponse(['error' => 'Invalid parameters', 'details' => $errors], Response::HTTP_BAD_REQUEST);
        }

        if ($matchType == MatchConfig::AVAILABLE_MATCH_TYPE[1] && $divisionId == 'all') {
            return new JsonResponse(['error' => 'Invalid parameters', 'details' => 'select parameter 4: (1, 2, 3, 4, 5, 6, 7, 8)'], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->getDivisionMatchesResult($numberOfDivisions, $teamType, $matchType, $divisionId);
        return $result;
    }

    private function getDivisionMatchesResult($numberOfDivisions, $teamType, $matchType, $divisionId): mixed
    {
        try {

            list($teams, $divisionNames) = $this->getTeamsAndDivisions($numberOfDivisions, $teamType, $matchType);

            if ($matchType == MatchConfig::AVAILABLE_MATCH_TYPE[0]) {
                $matchResult = $this->allMatchesService->createMatches($teams, $numberOfDivisions, $matchType, $divisionId);
            } else {
                $matchResult = $this->singleMatchService->createMatches($teams, $numberOfDivisions, $matchType, $divisionId);
            }

            return new JsonResponse(data: [
                'Matches' => $matchResult,
                'Divisions' => $divisionNames,
                'Teams' => $teams
            ]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getTeamsAndDivisions(int $numberOfDivisions, string $teamType, string $matchType): array
    {
        $teams = [];
        $divisionNames = [];
        $mixedArrayOfCacheKeys = ['divisionListArray', 'teamsListArray'];
        
        $divisionNames = $this->cacheService->cacheArray(key: $mixedArrayOfCacheKeys[0]);

        if (!empty($divisionNames) && count($divisionNames)!==$numberOfDivisions) {
            $this->cacheService->cacheArray(arr:$mixedArrayOfCacheKeys,action: 'delete');
        }

        if ($matchType === MatchConfig::AVAILABLE_MATCH_TYPE[1]) {
            $teams = $this->cacheService->cacheArray(key: $mixedArrayOfCacheKeys[1]);
            $divisionNames = $this->cacheService->cacheArray(key: $mixedArrayOfCacheKeys[0]);
            

            if (empty($teams) || empty($divisionNames)) {
                $this->clearData();
                return $this->generateNewData($numberOfDivisions, $teamType, $matchType);
            }
        } else {
            $this->clearData();
            return $this->generateNewData($numberOfDivisions, $teamType, $matchType);
        }

        return [$teams, $divisionNames];
    }

    private function generateNewData(int $numberOfDivisions, string $teamType, string $matchType): array
    {
        $divisions = $this->divisionService->createDivisions($numberOfDivisions);
        $divisionNames = array_map(fn($division) => $division->getName(), $divisions);
        $teams = $this->teamService->loadAndAssignTeams($numberOfDivisions, $teamType);


        if ($matchType === MatchConfig::AVAILABLE_MATCH_TYPE[1]) {
            $this->cacheService->cacheArray($teams, 'save', 'teamsListArray');
            $this->cacheService->cacheArray($divisionNames, 'save', 'divisionListArray');
        }

        return [$teams, $divisionNames];
    }



    private function validateParams(int $numberOfDivisions, string $teamType, string $matchType, $divisionId): array
    {
        $constraint = new Assert\Collection([
            'numberOfDivisions' => [
                new Assert\Positive(['message' => 'Paramater 1: must be a positive number.']),
                new Assert\Choice([
                    'choices' => MatchConfig::getAvailableDivisionNumber(),
                    'message' => 'Paramater 1: must be an integer between 1 and 8'
                ])

            ],
            'teamType' => [
                new Assert\Regex(['pattern' => '/^[a-zA-Z,]+$/', 'message' => 'Paramater 2: must be a string']),
                new Assert\Choice([
                    'choices' => MatchConfig::getAvailableTeamType(),
                    'message' => 'Parameter 2: Choose a valid team list type. (list, random)'
                ])
            ],
            'matchType' => [
                new Assert\Regex(['pattern' => '/^[a-zA-Z,]+$/', 'message' => 'Paramater 3: must be a string']),
                new Assert\Choice([
                    'choices' => MatchConfig::getAvailableMatchType(),
                    'message' => 'Parameter 3: Choose a valid team list type. (all, single)'
                ])
            ],
            'divisionId' => [
                new Assert\Choice([
                    'choices' => MatchConfig::getAvailableDivisionId(),
                    'message' => 'Parameter 4: Choose a valid team list type. (all, 1, 2, 3, 4, 5, 6, 7, 8)'
                ])
            ]
        ]);

        $violations = $this->validator->validate(compact('numberOfDivisions', 'teamType', 'matchType', 'divisionId'), $constraint);
        return array_map(fn($violation) => $violation->getMessage(), iterator_to_array($violations));

    }

    private function clearData()
    {
        $tables = ['teams_match', 'divisions', 'matches_hist'];
        foreach ($tables as $table) {
            $this->databaseService->clearTable($table);
        }
    }

}