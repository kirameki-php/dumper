<?php declare(strict_types=1);

namespace Kirameki\Dumper\Handlers;

use Kirameki\Dumper\Configs\DebugInfo;
use Kirameki\Dumper\ObjectTracker;
use ReflectionObject;
use ReflectionProperty;
use SouthPointe\Ansi\Codes\Color;
use function array_key_exists;
use function array_merge;
use function count;
use function method_exists;

class ClassHandler extends Handler
{
    /**
     * @param object $var
     * @param int $id
     * @param int $depth
     * @param ObjectTracker $tracker
     * @return string
     */
    public function handle(object $var, int $id, int $depth, ObjectTracker $tracker): string
    {
        $properties = $this->getProperties($var);

        $summary =
            $this->colorizeName($var::class) . ' ' .
            $this->colorizeComment("#{$id}");

        if (count($properties) === 0) {
            return $summary;
        }

        if ($tracker->isProcessed($id)) {
            $word = $tracker->isCircular($id) ? 'circular' : "recap {$tracker->getProcessedCount($id)}";
            return
                $summary . ' ' .
                $this->colorizeComment("<{$word}>") . ' ' .
                '{ ' .
                $this->colorizeComment('⋯') .
                ' }';
        }

        $tracker->markAsProcessed($id);

        $string = "{$summary} {" . $this->eol();
        foreach ($properties as $key => $val) {
            $string .= $this->line(
                $this->colorizeKey($key) .
                $this->colorizeDelimiter(':') . ' ' .
                $this->formatter->format($val, $depth + 1, $tracker),
                $depth + 1,
            );
        }
        $string .= $this->indent('}', $depth);

        $tracker->clearCircular($id);

        return $string;
    }

    /**
     * @param object $var
     * @return array<string, mixed>
     */
    protected function getProperties(object $var): array
    {
        $debugInfoOption = $this->config->debugInfo;

        if ($debugInfoOption === DebugInfo::Overwrite) {
            $debugInfo = $this->getDebugInfo($var);
            if ($debugInfo !== null) {
                return $debugInfo;
            }
        }

        $objReflection = new ReflectionObject($var);
        $propertyReflections = $objReflection->getProperties(
            $this->config->propertyFilter,
        );

        $properties = [];
        foreach ($propertyReflections as $reflection) {
            $access = ($reflection->getModifiers() & ReflectionProperty::IS_STATIC)
                ? 'static '
                : '';
            $name = $access . $reflection->getName();
            $value = $reflection->getValue($var);
            $properties[$name] = $value;
        }

        if ($debugInfoOption === DebugInfo::Append) {
            $debugInfo = $this->getDebugInfo($var);
            if ($debugInfo !== null) {
                $properties = array_merge($debugInfo, $properties);
            }
        }

        return $properties;
    }

    /**
     * @param object $var
     * @return array<string, mixed>|null
     */
    protected function getDebugInfo(object $var): ?array
    {
        if (!method_exists($var, '__debugInfo')) {
            return null;
        }

        return $var->__debugInfo();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function colorizeName(string $name): string
    {
        return $this->colorize($name, Color::DarkCyan);
    }
}
