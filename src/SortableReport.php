<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

interface SortableReport 
{
    /**
     * @param array|TestSummary[] $items
     * @return array|TestSummary[]
     */
    public function sortedDescending(array $items): array;
}
