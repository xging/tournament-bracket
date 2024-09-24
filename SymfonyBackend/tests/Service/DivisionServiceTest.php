<?PHP
namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\DivisionService;
use App\Service\Database\DatabaseServiceInterface;
use App\Entity\Divisions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DivisionServiceTest extends TestCase
{
    public function testCreateDivisionsSuccess()
    {
        $mockDatabaseService = $this->createMock(DatabaseServiceInterface::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $divisionA = $this->createMock(Divisions::class);
        $divisionB = $this->createMock(Divisions::class);
        $divisionC = $this->createMock(Divisions::class);

        $mockDatabaseService->expects($this->exactly(3))
            ->method('addDivision')
            ->withConsecutive(
                [$this->equalTo('Division A'), $this->equalTo(1)],
                [$this->equalTo('Division B'), $this->equalTo(2)],
                [$this->equalTo('Division C'), $this->equalTo(3)]
            )
            ->willReturnOnConsecutiveCalls($divisionA, $divisionB, $divisionC);

        $service = new DivisionService($mockDatabaseService, $mockLogger);

        $result = $service->createDivisions(3);

        $this->assertCount(3, $result);
        $this->assertSame([$divisionA, $divisionB, $divisionC], $result);
    }

    public function testCreateDivisionsInvalidNumbers()
    {
        $mockDatabaseService = $this->createMock(DatabaseServiceInterface::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $service = new DivisionService($mockDatabaseService, $mockLogger);

        $invalidNumbers = [9, 0];

        foreach ($invalidNumbers as $invalidNumber) {
            try {
                $service->createDivisions($invalidNumber);
                $this->fail("Not Expected number: $invalidNumber");
            } catch (BadRequestHttpException $e) {
                $this->assertStringContainsString('Invalid number of divisions', $e->getMessage());
            }
        }
    }
}
