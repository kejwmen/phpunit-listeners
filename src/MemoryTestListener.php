<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

use PHPUnit\Framework\BaseTestListener;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;

class MemoryTestListener extends BaseTestListener
{
    private const DEFAULT_MEMORY_THRESHOLD = 128;

    /** @var int */
    private $memoryBefore;
    /** @var int */
    private $memoryAfter;
    /** @var int */
    private $suites;
    /** @var array */
    private $results;
    /** @var int */
    private $memoryUsageThreshold;
    /** @var bool */
    private $reportBelowThreshold;
    /** @var bool */
    private $reportAboveThreshold;

    public function __construct(array $config)
    {
        $this->loadConfig($config);
        $this->suites = 0;
        $this->results = [];
    }

    /**
     * @inheritdoc
     */
    public function startTestSuite(TestSuite $suite)
    {
        $this->suites++;
    }

    /**
     * @inheritdoc
     */
    public function endTestSuite(TestSuite $suite)
    {
        $this->suites--;

        if ($this->suites === 0) {
            (new SymfonyConsoleMemoryReportWriter($this->results, $this->reportAboveThreshold, $this->reportBelowThreshold))->write();
        }
    }

    /**
     * @inheritdoc
     */
    public function startTest(Test $test)
    {
        $this->memoryBefore = memory_get_peak_usage(true);
    }

    /**
     * @inheritdoc
     */
    public function endTest(Test $test, $time)
    {
        if (!$test instanceof TestCase) {
            return null;
        }

        $this->memoryAfter = memory_get_peak_usage(true);

        $testUsage = $this->memoryAfter - $this->memoryBefore;

        $this->results[] = new TestMemoryResult(
            $test,
            $this->bytesToMegabytes($testUsage),
            (float) $this->memoryUsageThreshold
        );
    }

    private function bytesToMegabytes(int $bytes): float
    {
        return ((float) $bytes / 1024 / 1024);
    }

    /**
     * @param array $config
     */
    private function loadConfig(array $config): void
    {
        $this->memoryUsageThreshold = $config['memoryUsageThreshold'] ? (int) $config['memoryUsageThreshold'] : self::DEFAULT_MEMORY_THRESHOLD;
        $this->reportBelowThreshold = $config['reportBelowThreshold'] ?? false;
        $this->reportAboveThreshold = $config['reportAboveThreshold'] ?? true;

        $this->reportBelowThreshold = $config['maxBelowThreshold'] ?? 16;
        $this->reportAboveThreshold = $config['maxAboveThreshold'] ?? 16;
    }
}
