<?php

declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

interface ReportWriter
{
    /**
     * @param array|TestSummary[] $summaries
     */
    public function write(array $summaries) : void;
}
