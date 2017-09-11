<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners\Memory;

use kejwmen\PhpUnitListeners\Report;
use kejwmen\PhpUnitListeners\SortableReport;
use kejwmen\PhpUnitListeners\SummaryRenderer;
use kejwmen\PhpUnitListeners\TestSummary;

class TestsExceedingMemoryThresholdReport implements Report, SortableReport
{
    /**
     * @var int
     */
    private $limit;
    /**
     * @var SummaryRenderer
     */
    private $renderer;
    /**
     * @var string
     */
    private $name;

    public function __construct(int $limit = 10, string $name = 'Tests exceeded the most', SummaryRenderer $renderer = null)
    {
        $this->limit = $limit;
        $this->renderer = $renderer ?? new TestsExceedingMemoryThresholdRenderer();

        $this->name = $name;
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->limit;
    }

    public function render(TestSummary $summary): array
    {
        return $this->renderer->renderSummary($summary);
    }

    public function includesSummary(TestSummary $result): bool
    {
        return $result instanceof MemoryTestSummary && $result->exceededThresholdBy() > 0;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param array|MemoryTestSummary[] $items
     * @return array|MemoryTestSummary[]
     */
    public function sortedDescending(array $items): array
    {
        return \Functional\sort($items, function (MemoryTestSummary $current, MemoryTestSummary $previous) {
            return $previous->exceededThresholdBy() <=> $current->exceededThresholdBy();
        });
    }

    public function headers(): array
    {
        return $this->renderer->renderHeaders();
    }
}
