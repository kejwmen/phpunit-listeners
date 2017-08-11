<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

use function Functional\map;
use function Functional\select;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyConsoleMemoryReportWriter implements MemoryReportWriter
{
    /** @var array|TestMemoryResult[] */
    private $items;
    /** @var SymfonyStyle */
    private $output;
    /** @var bool */
    private $writeAbove;
    /** @var bool */
    private $writeBelow;
    /** @var int */
    private $maxAbove;
    /** @var int */
    private $maxBelow;

    public function __construct(
        array $items,
        bool $writeAbove = true,
        bool $writeBelow = false,
        int $maxAbove = 8,
        int $maxBelow = 8
    ) {
        $this->items = $items;
        $this->output = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

        $this->writeAbove = $writeAbove;
        $this->writeBelow = $writeBelow;
        $this->maxAbove = $maxAbove;
        $this->maxBelow = $maxBelow;
    }

    public function write(): void
    {
        $this->writeHeader($this->items);

        if ($this->writeAbove) {
            $this->writeExceeded($this->items);
        }

        if ($this->writeBelow) {
            $this->writeClosest($this->items);
        }
    }


    private function writeHeader(array $items): void
    {
        $this->output->newLine();
        $this->output->title("Memory usage report");
    }

    private function writeExceeded(array $items): void
    {
        $above = select($items, function (TestMemoryResult $item) {
            return $item->exceededThresholdBy() > 0;
        });

        usort($above, function (TestMemoryResult $current, TestMemoryResult $previous) {
            return $previous->exceededThresholdBy() <=> $current->exceededThresholdBy();
        });

        $rendered = map($above, function (TestMemoryResult $item) {
            return $item->render();
        });

        if (count($rendered) > 0) {
            $this->output->error(sprintf(
                "%d tests exceeded memory usage limit",
                count($rendered)
            ));
        } else {
            $this->output->success(sprintf(
                "All tests in memory usage limit"
            ));
        }

        $this->output->table(
            ['Name', 'Threshold (MB)', 'Usage (MB)', 'Exceeded by (MB)'],
            array_slice($rendered, 0, $this->maxAbove)
        );
    }

    private function writeClosest(array $items): void
    {
        $below = select($items, function (TestMemoryResult $item) {
            return $item->exceededThresholdBy() < 0;
        });

        usort($below, function (TestMemoryResult $current, TestMemoryResult $previous) {
            return $previous->exceededThresholdBy() <=> $current->exceededThresholdBy();
        });

        $rendered = map($below, function (TestMemoryResult $item) {
            return $item->render();
        });

        $this->output->warning(sprintf(
            "%d tests close to memory usage limit",
            count($rendered)
        ));

        $output = array_slice($rendered, 0, $this->maxBelow);

        $this->output->table(
            ['Name', 'Threshold (MB)', 'Usage (MB)', 'Free memory (MB)'],
            $output
        );
    }
}
