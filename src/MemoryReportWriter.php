<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

interface MemoryReportWriter
{
    public function write(): void;
}
