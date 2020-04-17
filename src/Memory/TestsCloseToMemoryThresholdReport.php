<?php

declare(strict_types=1);

namespace kejwmen\PhpUnitListeners\Memory;

use Assert\Assert;
use kejwmen\PhpUnitListeners\Report;
use kejwmen\PhpUnitListeners\SortableReport;
use kejwmen\PhpUnitListeners\SummaryRenderer;
use kejwmen\PhpUnitListeners\TestSummary;
use function Functional\sort;

class TestsCloseToMemoryThresholdReport implements Report, SortableReport
{
    /** @var int */
    private $limit;

    /** @var SummaryRenderer */
    private $renderer;

    /** @var string */
    private $name;

    public function __construct(
        int $limit = 10,
        string $name = 'Tests below threshold',
        ?SummaryRenderer $renderer = null
    ) {
        $this->limit    = $limit;
        $this->renderer = $renderer ?? new TestsCloseToMemoryThresholdRenderer();
        $this->name     = $name;
    }

    public function limit() : int
    {
        return $this->limit;
    }

    /***
     * @param MemoryTestSummary $summary
     */
    public function includesSummary(TestSummary $summary) : bool
    {
        Assert::that($summary)->isInstanceOf(MemoryTestSummary::class);

        return $summary->usage() > 0 && $summary->exceededThresholdBy() < 0;
    }

    public function name() : string
    {
        return $this->name;
    }

    /**
     * @return mixed[]
     */
    public function render(TestSummary $summary) : array
    {
        return $this->renderer->renderSummary($summary);
    }

    /**
     * @param MemoryTestSummary[] $items
     *
     * @return MemoryTestSummary[]
     */
    public function sortedDescending(array $items) : array
    {
        return sort(
            $items,
            static function (MemoryTestSummary $current, MemoryTestSummary $previous) {
                return $previous->exceededThresholdBy() <=> $current->exceededThresholdBy();
            }
        );
    }

    /**
     * @return string[]
     */
    public function headers() : array
    {
        return $this->renderer->renderHeaders();
    }
}
