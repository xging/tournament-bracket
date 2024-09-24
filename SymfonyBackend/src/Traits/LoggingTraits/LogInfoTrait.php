<?PHP
namespace App\Traits\LoggingTraits;

trait LogInfoTrait {
    public function saveLogInfo(string $message, array $context = []): void
    {
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $value = implode(", ", $value);
            }
            $message = str_replace("{" . $key . "}", (string) $value, $message);
        }
        $this->logger->info($message, $context);
    }
}