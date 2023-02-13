<?php declare(strict_types=1);

namespace Tests\Kirameki\Dumper\Samples;

class CircularClass
{
    public self $next;
}
