<?php

declare(strict_types=1);

namespace kejwmen\PhpUnitListeners;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use function array_slice;
use function count;
use function Functional\each;
use function Functional\map;
use function Functional\select;
use function sprintf;

class SymfonyConsoleReportWriter implements ReportWriter
{
    /** @var SymfonyStyle */
    private $output;

    /** @var Report[] */
    private $reports;

    /**
     * @param Report[] $reports
     */
    public function __construct(array $reports)
    {
        $this->output = new SymfonyStyle(new ArgvInput(), new ConsoleOutput());

        $this->reports = $reports;
    }

    /**
     * @param TestSummary[] $summaries
     */
    public function write(array $summaries) : void
    {
        $this->writeHeader();

        each(
            $this->reports,
            function (Report $spec) use ($summaries) : void {
                $this->writeForSpec($summaries, $spec);
            }
        );
    }

    private function writeHeader() : void
    {
        $this->output->newLine();
        $this->output->title('Memory usage report');
    }

    /**
     * @param TestSummary[] $summaries
     */
    private function writeForSpec(array $summaries, Report $reportSpec) : void
    {
        $this->output->section($reportSpec->name());

        $summaries = select(
            $summaries,
            static function (TestSummary $item) use ($reportSpec) {
                return $reportSpec->includesSummary($item);
            }
        );

        if (count($summaries) > 0) {
            $summaries = array_slice($summaries, 0, $reportSpec->limit());

            if ($reportSpec instanceof SortableReport) {
                $summaries = $reportSpec->sortedDescending($summaries);
            }

            $renderedItems = map(
                $summaries,
                static function (TestSummary $item) use ($reportSpec) {
                    return $reportSpec->render($item);
                }
            );

            $this->output->error(
                sprintf(
                    '%d reported tests',
                    count($renderedItems)
                )
            );

            $this->output->table(
                $reportSpec->headers(),
                array_slice($renderedItems, 0, $reportSpec->limit())
            );
        } else {
            $this->output->success(
                sprintf(
                    'No reported tests'
                )
            );
        }
    }
}
