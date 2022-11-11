<?php declare(strict_types=1);

namespace SouthPointe\DataDump\Formatters;

use Closure;
use ReflectionFunction;
use function assert;

class ClosureFormatter extends ClassFormatter
{
    /**
     * @param Closure $var
     * @inheritDoc
     */
    public function format(object $var, int $id, int $depth, array $objectIds): string
    {
        assert($var instanceof Closure);

        $deco = $this->decorator;

        $ref = new ReflectionFunction($var);

        if ($file = $ref->getFileName()) {
            $startLine = $ref->getStartLine();
            $endLine = $ref->getEndLine();
            $range = ($startLine !== $endLine)
                ? "{$startLine}-{$endLine}"
                : $startLine;
            return
                $deco->classType($var::class . "@{$file}:{$range}") . ' ' .
                $deco->comment("#{$id}");
        }

        if ($class = $ref->getClosureScopeClass()) {
            return
                $deco->classType("{$class->getName()}::{$ref->getName()}(...)") . ' ' .
                $deco->comment("#{$id}");
        }

        return
            $deco->classType("{$ref->getName()}(...)") . ' ' .
            $deco->comment("#{$id}");
    }
}