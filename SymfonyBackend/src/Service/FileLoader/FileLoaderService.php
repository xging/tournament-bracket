<?php
namespace App\Service\FileLoader;

use Symfony\Component\HttpKernel\KernelInterface;

class FileLoaderService implements FileLoaderServiceInterface
{
    private KernelInterface $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    public function loadTeams(): array
    {
        $filePath = $this->kernel->getProjectDir() . '/assets/teams.json';
        if (!file_exists($filePath)) {
            throw new \RuntimeException('JSON file not found.');
        }

        $jsonData = file_get_contents($filePath);
        $data = json_decode($jsonData, true);

        if (!isset($data['teams']) || !is_array($data['teams'])) {
            throw new \UnexpectedValueException('Invalid data format in JSON file.');
        }

        return $data['teams'];
    }
}