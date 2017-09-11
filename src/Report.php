<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

interface Report
{
    public function name(): string;
    public function limit(): int;
    public function render(TestSummary $summary): array;
    public function headers(): array;
    public function includesSummary(TestSummary $summary): bool;
}
