<?php declare(strict_types=1);

namespace Kirameki\Dumper;

class Dumper
{
    /**
     * @param Config $config
     */
    public function __construct(
        protected Config $config = new Config(),
    )
    {
    }

    /**
     * @param mixed $var
     * @return void
     */
    public function dump(mixed $var): void
    {
        $config = $this->config;
        $format = $config->formatter->format($var);
        $config->writer->write($format);
    }
}
