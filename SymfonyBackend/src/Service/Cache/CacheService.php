<?PHP
namespace App\Service\Cache;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Service\Cache\CacheServiceInterface;

#[WithMonologChannel('CacheServiceController')]
class CacheService implements CacheServiceInterface
{
    private LoggerInterface $logger;
    private const AVAILABLE_CACHE_KEY = ['teamsListArray', 'divisionListArray', 'matchesListArray', 'newMatches'];
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function cacheArray($arr = [], $action = 'get', $key = 'defaultCacheKey'): array
    {
        $cache = new FilesystemAdapter();
        $keyListCache = $cache->getItem($key);


        if (!$keyListCache->isHit()) {
            $keyList = [];
        } else {
            $keyList = $keyListCache->get();
        }

        if ($action == 'save') {
            if ($key == self::AVAILABLE_CACHE_KEY[2] || $key == self::AVAILABLE_CACHE_KEY[3]) {
                $keyList[] = $arr;
            } else {
                $keyList = $arr;
            }
            $keyListCache->set($keyList);
            $keyListCache->expiresAfter(500);
            $cache->save($keyListCache);
        }

        if ($action == 'delete') {

            // foreach ($arr as $keys) {
            //     $cache->delete($keys);
            // }
            $cache->clear();
        }

        return $keyList;
    }
}