<?PHP
namespace App\Controller\Api;

use App\Service\DivisionService;
use App\Service\TeamService;
use App\Service\Database\DatabaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GenerateTeams extends AbstractController
{
    private $divisionService;
    private $teamService;
    private $allMatchesService;
    private $databaseService;
    public function __construct(DivisionService $divisionService, TeamService $teamService, DatabaseService $databaseService)
    {
        $this->divisionService = $divisionService;
        $this->teamService = $teamService;
        $this->databaseService = $databaseService;
    }

    #[Route('/api/generate-teams', name: 'generate-teams')]
    public function addData(): Response
    {
        try {
            $this->clearData();
            $teams = $this->teamService->loadAndAssignTeams(6, 'all');
            return new JsonResponse([
                'Teams' => $teams
            ]);

        } catch (BadRequestHttpException $e) {
            return new JsonResponse(['Error createDivisions' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\RuntimeException $e) {
            return new JsonResponse(['Error createDivisions' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    private function clearData()
    {
        $tables = ['teams_match'];
        foreach ($tables as $table) {
            $this->databaseService->clearTable($table);
        }
    }
}