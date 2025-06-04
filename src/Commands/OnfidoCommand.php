<?php

namespace OANNA\Onfido\Commands;

use Illuminate\Console\Command;

class OnfidoCommand extends Command
{
    public $signature = 'onfido';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
