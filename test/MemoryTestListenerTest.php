<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners\Test;

use PHPUnit\Framework\TestCase;

class MemoryTestListenerTest extends TestCase
{
    /**
     * @memoryUsageThreshold 1
     */
    public function testItUsesMethodThreshold()
    {
        $array = [
            str_repeat('a', 2 * 1024 * 1024),
            str_repeat('b', 1 * 1024 * 1024),
            str_repeat('c', 1 * 1024 * 1024)
        ];

        self::assertTrue(true);
    }

    public function testItExceedesDefaultMethodThreshold()
    {
        $array = [
            str_repeat('a', 16 * 1024 * 1024),
            str_repeat('b', 8 * 1024 * 1024)
        ];

        self::assertTrue(true);
    }

    public function testItStaysBelowDefaultMethodThreshold()
    {
        $array = [
            str_repeat('a', 1 * 1024 * 1024)
        ];

        self::assertTrue(true);
    }

    /**
     * @memoryUsageThreshold 3
     */
    public function testItStaysBelowCustomThreshold()
    {
        $array = [
            str_repeat('a', 4 * 1024 * 1024)
        ];

        self::assertTrue(true);
    }
}
