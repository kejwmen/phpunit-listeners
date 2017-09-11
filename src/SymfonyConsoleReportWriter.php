<?php
declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

class SymfonyConsoleReportWriter implements ReportWriter
{
    /** @var SymfonyStyle */
    private $output;
    private $reports;

    public function __construct(
        array $reports
    ) {
        $this->output = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

        $this->reports = $reports;
    }

    public function write(array $items): void
    {
        $this->writeHeader();

        \Functional\each($this->reports, function (Report $spec) use ($items) {
            $this->writeForSpec($items, $spec);
        });
    }

    private function writeHeader(): void
    {
        $this->output->newLine();
        $this->output->title("Memory usage report");
    }

    private function writeForSpec(array $items, Report $reportSpec): void
    {
        $this->output->section($reportSpec->name());

        $items = \Functional\select($items, function (TestSummary $item) use ($reportSpec) {
            return $reportSpec->includesSummary($item);
        });

        if (count($items) > 0) {
            $items = array_slice($items, 0, $reportSpec->limit());

            if ($reportSpec instanceof SortableReport) {
                $items = $reportSpec->sortedDescending($items);
            }

            $renderedItems = \Functional\map($items, function (TestSummary $item) use ($reportSpec) {
                return $reportSpec->render($item);
            });

            $this->output->error(sprintf(
                "%d reported tests",
                count($renderedItems)
            ));

            $this->output->table(
                $reportSpec->headers(),
                array_slice($renderedItems, 0, $reportSpec->limit())
            );
        } else {
            $this->output->success(sprintf(
                "No reported tests"
            ));
        }
    }
}
