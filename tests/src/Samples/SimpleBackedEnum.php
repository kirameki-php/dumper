<?php declare(strict_types=1);

namespace Tests\Kirameki\Dumper\Samples;

enum SimpleBackedEnum: string
{
    case Option1 = 'one';
    case Option2 = 'two';
}
