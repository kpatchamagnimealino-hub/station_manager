<?php

namespace App\Console\Commands;

use App\Services\StationReportGenerator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('reports:generate-daily')]
#[Description('Generate and archive daily station sales and stock reports')]
class GenerateDailyStationReports extends Command
{
    public function handle(StationReportGenerator $reportGenerator): int
    {
        $reports = $reportGenerator->generateDaily();

        $this->info('Rapports du '.$reports['date'].' générés :');
        $this->line($reports['pdf']);
        $this->line($reports['csv']);

        return self::SUCCESS;
    }
}
