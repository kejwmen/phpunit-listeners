<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners\Memory;

use kejwmen\PhpUnitListeners\TestSummary;
use PHPUnit\Framework\TestCase;

class MemoryTestSummary implements TestSummary
{
    private const MEMORY_USAGE_ANNOTATION_NAME = 'memoryUsageThreshold';

    /** @var TestCase */
    private $test;
    /** @var float */
    private $testUsage;
    /** @var int */
    private $threshold;

    public function __construct(TestCase $test, float $memoryUsage, int $defaultThreshold)
    {
        $this->test = $test;
        $this->testUsage = $memoryUsage;

        $this->threshold = $this->calculateThreshold($defaultThreshold);
    }

    public function hasExceededThreshold(): bool
    {
        return $this->testUsage > $this->threshold;
    }

    public function exceededThresholdBy(): float
    {
        return $this->testUsage - $this->threshold;
    }

    private function calculateThreshold(int $defaultThreshold): int
    {
        $testAnnotations = $this->test->getAnnotations();
        $methodThreshold = $this->getAnnotationValue($testAnnotations, 'method', self::MEMORY_USAGE_ANNOTATION_NAME);
        $classThreshold = $this->getAnnotationValue($testAnnotations, 'class', self::MEMORY_USAGE_ANNOTATION_NAME);

        $threshold = $methodThreshold ?? $classThreshold ?? $defaultThreshold;

        return (int) $threshold;
    }

    private function getAnnotationValue(array $annotationsArray, $type, $name): ?string
    {
        return $annotationsArray[$type][$name][0] ?? null;
    }

    public function usage(): float
    {
        return $this->testUsage;
    }

    /**
     * @return int
     */
    public function threshold(): int
    {
        return $this->threshold;
    }

    /**
     * @return TestCase
     */
    public function test(): TestCase
    {
        return $this->test;
    }
}
