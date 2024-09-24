<?php
namespace App\Service;
use App\Service\Database\DatabaseServiceInterface;
use App\Service\Interfaces\DivisionServiceInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[WithMonologChannel('DivisionService')]
class DivisionService implements DivisionServiceInterface
{
    private $databaseService;
    private $logger;

    public function __construct(DatabaseServiceInterface $databaseService, LoggerInterface $logger)
    {
        $this->databaseService = $databaseService;
        $this->logger = $logger;
    }

    public function createDivisions(int $numOfDivisions): array
    {
        $divisions = [];

        if ($numOfDivisions > 8 || $numOfDivisions < 1) {
            $this->logger->error('Invalid number of divisions, number should be `higher or equal to 1` `less or equal to 8`, current: {numOfDivisions}', ['numOfDivisions' => $numOfDivisions]);
            throw new BadRequestHttpException('Invalid number of divisions, number should be `higher or equal to 1` `less or equal to 8`, current: ' . $numOfDivisions);
        }

        try {
            for ($i = 1; $i <= $numOfDivisions; $i++) {
                $letter = $this->getIndexLetter($i - 1);
                $division = $this->databaseService->addDivision('Division ' . $letter, $i);
                $divisions[] = $division;
            }
            $this->logger->info("Division created:", ['Division' => $divisions]);
        } catch (\Exception $e) {
            $this->logger->error('Error occurred in createDivisions(): ' . $e->getMessage(), [
                'exception' => $e
            ]);
            throw new \RuntimeException('An error occurred while creating divisions.');
        }

        return $divisions;
    }

    private function getIndexLetter($index): string
    {
        $alphabet = range('A', 'Z');
        $result = '';

        while ($index >= 0) {
            $result = $alphabet[$index % 26] . $result;
            $index = floor($index / 26) - 1;
        }

        return $result;
    }
}
