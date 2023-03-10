<?php declare(strict_types=1);

namespace Tests\Kirameki\Dumper\Samples;

class DebuggableClass
{
    /**
     * @return array<string, int>|null
     */
    public function __debugInfo(): ?array
    {
        return [
            'foo' => 1,
            'bar' => 2,
        ];
    }
}
