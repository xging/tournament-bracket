<?PHP
namespace App\Service\Interfaces;

interface ResultUpdateServiceInterface
{
    public function updateResult(string $team): void;
}