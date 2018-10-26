<?php

declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

interface SummaryRenderer
{
    /**
     * @return string[]
     */
    public function renderHeaders() : array;

    /**
     * @return mixed[]
     */
    public function renderSummary(TestSummary $summary) : array;
}
