<?php

namespace BellissimoPizza\RequestDtoGenerator\Commands;

use Illuminate\Console\Command;
use BellissimoPizza\RequestDtoGenerator\Version;

class VersionCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'dto:version 
                            {--json : Output version information as JSON}
                            {--full : Show full package identifier}';

    /**
     * The console command description.
     */
    protected $description = 'Display the version of the Laravel Request DTO Generator package';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('json')) {
            $this->outputJson();
            return 0;
        }

        if ($this->option('full')) {
            $this->outputFull();
            return 0;
        }

        $this->outputStandard();
        return 0;
    }

    /**
     * Output standard version information
     */
    protected function outputStandard(): void
    {
        $this->info('Laravel Request DTO Generator');
        $this->line('Version: ' . Version::getVersion());
        $this->line('Package: ' . Version::getPackageName());
    }

    /**
     * Output full package identifier
     */
    protected function outputFull(): void
    {
        $this->info(Version::getFullIdentifier());
    }

    /**
     * Output version information as JSON
     */
    protected function outputJson(): void
    {
        $this->line(json_encode(Version::getInfo(), JSON_PRETTY_PRINT));
    }
}
