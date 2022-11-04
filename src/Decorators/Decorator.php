<?php declare(strict_types=1);

namespace SouthPointe\DataDump\Decorators;

interface Decorator
{
    public function output(string $string): void;

    public function indent(string $string, int $depth): string;

    public function line(string $string, int $depth): string;

    public function eol(): string;

    public function type(string $type): string;

    public function scalar(mixed $value): string;

    public function parameterKey(int|string $key): string;

    public function parameterDelimiter(string $delimiter): string;

    public function comment(string $comment): string;
}
