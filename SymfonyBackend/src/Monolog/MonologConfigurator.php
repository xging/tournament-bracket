<?php
namespace App\Monolog;

use Monolog\Formatter\LineFormatter;

/**
 * Custom formatter for Monolog to modify log output format.
 */
class MonologConfigurator extends LineFormatter
{
    /**
     * Constructor.
     *
     * @param string|null $format
     * @param string|null $dateFormat
     * @param bool $allowInlineLineBreaks
     * @param bool $ignoreEmptyContextAndExtra
     */
    public function __construct(
        $format = "[%datetime%] %channel%.%level_name%: %message% | Context: %context%\n",
        $dateFormat = 'Y-m-d H:i:s',
        $allowInlineLineBreaks = false,
        $ignoreEmptyContextAndExtra = false
    ) {
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, true);
    }
}
