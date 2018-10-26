<?php

declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

use PHPUnit\Framework\TestCase;

interface TestSummary
{
    public function test() : TestCase;
}
