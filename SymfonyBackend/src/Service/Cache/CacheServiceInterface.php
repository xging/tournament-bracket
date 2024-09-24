<?PHP
namespace App\Service\Cache;

interface CacheServiceInterface
{
    public function cacheArray($arr, $action, $key): array;
}