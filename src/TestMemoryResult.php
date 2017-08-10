<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

use PHPUnit\Framework\TestCase;

class TestMemoryResult
{
    private const MEMORY_USAGE_ANNOTATION_NAME = 'memoryUsageThreshold';

    /** @var TestCase */
    private $test;
    /** @var float */
    private $testUsage;
    /** @var float */
    private $defaultThreshold;
    /** @var int */
    private $threshold;

    public function __construct(TestCase $test, float $testUsage, float $defaultThreshold)
    {
        $this->test = $test;
        $this->testUsage = $testUsage;
        $this->defaultThreshold = $defaultThreshold;

        $this->threshold = $this->getThreshold();
    }

    public function hasExceededThreshold(): bool
    {
        return $this->testUsage > $this->threshold;
    }

    public function exceededThresholdBy(): float
    {
        return $this->testUsage - $this->threshold;
    }

    private function getThreshold(): int
    {
        $testAnnotations = $this->test->getAnnotations();
        $methodThreshold = $this->getAnnotationValue($testAnnotations, 'method', self::MEMORY_USAGE_ANNOTATION_NAME);
        $classThreshold = $this->getAnnotationValue($testAnnotations, 'class', self::MEMORY_USAGE_ANNOTATION_NAME);
        $defaultThreshold = $this->defaultThreshold;

        $threshold = $methodThreshold ?? $classThreshold ?? $defaultThreshold;

        return (int) $threshold;
    }

    private function getAnnotationValue(array $annotationsArray, $type, $name): ?string
    {
        return $annotationsArray[$type][$name][0] ?? null;
    }

    public function render(): array
    {
        return [
            'name' => $this->test->toString(),
            'threshold' => sprintf('%.2f', $this->threshold),
            'usage' => sprintf('%.2f', $this->testUsage),
            'exceeded' => sprintf('%.2f', $this->testUsage - $this->threshold)
        ];
    }
}
