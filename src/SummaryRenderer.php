<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

interface SummaryRenderer 
{
    public function renderHeaders(): array;
    public function renderSummary(TestSummary $summary): array;
}
