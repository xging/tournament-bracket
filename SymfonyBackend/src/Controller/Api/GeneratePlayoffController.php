<?PHP
namespace App\Controller\Api;
use App\Service\Database\DatabaseService;
use App\Service\Matches\PlayoffMatches\PlayoffConfig;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\Matches\PlayoffMatches\AllPlayoffMatchesService;

class GeneratePlayoffController extends AbstractController
{
    private AllPlayoffMatchesService $allPlayoffMatchesService;
    private DatabaseService $databaseService;
    private ValidatorInterface $validator;

    public function __construct(
        AllPlayoffMatchesService $allPlayoffMatchesService,
        DatabaseService $databaseService,
        ValidatorInterface $validator
    ) {
        $this->allPlayoffMatchesService = $allPlayoffMatchesService;
        $this->databaseService = $databaseService;
        $this->validator = $validator;
    }

    #[Route('/api/generate-playoff-data/{stage}', name: 'generate-playoff-data', defaults: ['stage' => 'all'])]
    public function generatePlayoffMatches($stage): Response
    {
        $stage = (string) $stage;
        $errors = $this->validateParams($stage);

        if (!empty($errors)) {
            return new JsonResponse(['error' => 'Invalid parameters', 'details' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $result = $this->getPlayoffMatchesResult($stage);
        return $result;
    }


    private function getPlayoffMatchesResult($stage): mixed
    {
        try {
            $this->clearData($stage);
            $availableStages = PlayoffConfig::getStagesList();
            $stageMatches = [];
            foreach ($availableStages as $currentStage) {
                if ($currentStage == $availableStages[6]) {
                    break;
                }

                if ($stage == $availableStages[6] || $currentStage == $stage) {
                    $playoffMatchResult = $this->allPlayoffMatchesService->createPlayoffMatches($currentStage, $availableStages);
                    $stageMatches[$currentStage] = $playoffMatchResult[$currentStage];

                    if ($stage != $availableStages[6]) {
                        break;
                    }
                }
            }

            return new JsonResponse([
                'PlayoffMatches' => $stageMatches
            ]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validateParams(string $stage): array
    {
        $constraint = new Assert\Collection([
            'stage' => [
                new Assert\Regex(['pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => 'Paramater 1: must be a string']),
                new Assert\Choice([
                    'choices' => PlayoffConfig::getStagesList(),
                    'message' => 'Parameter 1: Choose a valid stage. (round_1, round_2, quarterfinal, semifinal, bronzemedal, grandfinal, all)'
                ])
            ]
        ]);

        $violations = $this->validator->validate(compact('stage'), $constraint);
        return array_map(fn($violation) => $violation->getMessage(), iterator_to_array($violations));
    }

    private function clearData($stage)
    {
        $this->databaseService->clearColumnMatches($stage);
    }
}