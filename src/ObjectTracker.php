<?php declare(strict_types=1);

namespace Kirameki\Dumper;

use function array_key_exists;

class ObjectTracker
{
    /**
     * @param array<int, true> $processedIds
     * @param array<int, true> $circularIds
     */
    public function __construct(
        protected array $processedIds = [],
        protected array $circularIds = [],
    )
    {
    }

    public function markAsProcessed(int $id): void
    {
        $this->processedIds[$id] = true;
        $this->circularIds[$id] = true;
    }

    public function isProcessed(int $id): bool
    {
        return array_key_exists($id, $this->processedIds);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isCircular(int $id): bool
    {
        return array_key_exists($id, $this->circularIds);
    }

    /**
     * @param int $id
     * @return void
     */
    public function clearCircular(int $id): void
    {
        unset($this->circularIds[$id]);
    }
}
