<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

interface ReportWriter 
{
    /**
     * @param array|TestSummary[] $items
     */
    public function write(array $items): void;
}
