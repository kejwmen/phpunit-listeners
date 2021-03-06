<?php

declare(strict_types=1);

namespace kejwmen\PhpUnitListeners\Memory;

use Assert\Assert;
use kejwmen\PhpUnitListeners\SummaryRenderer;
use kejwmen\PhpUnitListeners\TestSummary;
use function sprintf;

class TestsCloseToMemoryThresholdRenderer implements SummaryRenderer
{
    /**
     * @return string[]
     */
    public function renderHeaders() : array
    {
        return ['Name', 'Threshold (MB)', 'Usage (MB)', 'Below threshold (MB)'];
    }

    /**
     * @param MemoryTestSummary $summary
     *
     * @return mixed[]
     */
    public function renderSummary(TestSummary $summary) : array
    {
        Assert::that($summary)->isInstanceOf(MemoryTestSummary::class);

        return [
            'name' => $summary->test()->toString(),
            'threshold' => sprintf('%.2f', $summary->threshold()),
            'usage' => sprintf('%.2f', $summary->usage()),
            'below' => sprintf('%.2f', -$summary->exceededThresholdBy()),
        ];
    }
}
