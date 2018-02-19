<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners\Memory;

use kejwmen\PhpUnitListeners\SymfonyConsoleReportWriter;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

class MemoryTestListener implements TestListener
{
    use TestListenerDefaultImplementation;

    private const DEFAULT_MEMORY_THRESHOLD = 128;
    private const DEFAULT_REPORT_LIMIT = 10;

    /** @var int */
    private $memoryBefore;
    /** @var int */
    private $memoryAfter;
    /** @var int */
    private $suites;
    /** @var array */
    private $results;
    /** @var array */
    private $config;
    /** @var array */
    private $reports;

    public function __construct(array $config, array $reports = [])
    {
        $this->loadConfig($config);
        $this->loadReports($reports);
        $this->suites = 0;
        $this->results = [];
    }

    /**
     * @inheritdoc
     */
    public function startTestSuite(TestSuite $suite): void
    {
        $this->suites++;
    }

    /**
     * @inheritdoc
     */
    public function endTestSuite(TestSuite $suite): void
    {
        $this->suites--;

        if ($this->suites === 0) {
            (new SymfonyConsoleReportWriter($this->reports))->write($this->results);
        }
    }

    /**
     * @inheritdoc
     */
    public function startTest(Test $test): void
    {
        $this->memoryBefore = memory_get_usage(true);
    }

    /**
     * @inheritdoc
     */
    public function endTest(Test $test, float $time): void
    {
        if (!$test instanceof TestCase) {
            return;
        }

        $this->memoryAfter = memory_get_usage(true);

        $testUsage = $this->memoryAfter - $this->memoryBefore;

        $this->results[] = new MemoryTestSummary(
            $test,
            $this->bytesToMegabytes($testUsage),
            $this->config['memoryUsageThreshold']
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
        $this->config = [
            'memoryUsageThreshold' => $config['memoryUsageThreshold'] ? (int) $config['memoryUsageThreshold'] : self::DEFAULT_MEMORY_THRESHOLD
        ];
    }

    private function defaultReports(): array
    {
        return [
            new TestsExceedingMemoryThresholdReport(
                self::DEFAULT_REPORT_LIMIT
            ),
            new TestsCloseToMemoryThresholdReport(
                self::DEFAULT_REPORT_LIMIT
            )
        ];
    }

    private function loadReports(?array $reports): void
    {
        $this->reports = $reports ?? $this->defaultReports();
    }
}
