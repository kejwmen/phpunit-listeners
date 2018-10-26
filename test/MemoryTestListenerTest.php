<?php

declare(strict_types=1);

namespace kejwmen\PhpUnitListeners\Test;

use kejwmen\PhpUnitListeners\Memory\MemoryTestListener;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use function random_bytes;
use function str_repeat;

class MemoryTestListenerTest extends TestCase
{
    public function testItUsesMethodThreshold() : void
    {
        // GIVEN
        $array = [
            $this->allocateMegabytesOfMemory(2),
            $this->allocateMegabytesOfMemory(1),
            $this->allocateMegabytesOfMemory(1),
        ];

        $listener = new MemoryTestListener(['memoryUsageThreshold' => 1]);

        /** @var TestCase|MockObject  $test */
        $test = $this->createMock(TestCase::class);

        $test->method('getAnnotations')
            ->willReturn([]);

        // WHEN
        $listener->endTest($test, 0.0);

        self::assertTrue(true);
    }

    public function testItStaysBelowDefaultMethodThreshold() : void
    {
        $array = [$this->allocateMegabytesOfMemory(8)];

        self::assertTrue(true);
    }


    public function testItExceedesDefaultMethodThreshold() : void
    {
        $array = [
            $this->allocateMegabytesOfMemory(16),
            $this->allocateMegabytesOfMemory(8),
        ];

        self::assertTrue(true);
    }

    public function testItStaysBelowCustomThreshold() : void
    {
        $array = [$this->allocateMegabytesOfMemory(48)];

        self::assertTrue(true);
    }

    private function allocateMegabytesOfMemory(int $megabytes) : string
    {
        return (string) (static function (int $megabytes) {
            return str_repeat(random_bytes(1), $megabytes * 1024 * 1024);
        })($megabytes);
    }
}
