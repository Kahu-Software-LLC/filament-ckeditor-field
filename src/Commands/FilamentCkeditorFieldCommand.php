<?php

namespace kahusoftware\FilamentCkeditorField\Commands;

use Illuminate\Console\Command;

class FilamentCkeditorFieldCommand extends Command
{
    public $signature = 'filament-ckeditor-field';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
