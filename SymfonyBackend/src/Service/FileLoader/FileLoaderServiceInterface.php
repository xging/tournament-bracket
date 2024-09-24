<?PHP
namespace App\Service\FileLoader;

interface FileLoaderServiceInterface
{
    public function loadTeams(): array;
}