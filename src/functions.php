<?php declare(strict_types=1);

use Kirameki\Dumper\Config;
use Kirameki\Dumper\Dumper;

if (!function_exists('dump'))
{
    /**
     * @param mixed $var
     * @param Config|null $config
     * @return void
     */
    function dump(mixed $var, ?Config $config = null): void
    {
        $dumper = new Dumper(config: $config ?? new Config());
        $dumper->dump($var);
    }
}
