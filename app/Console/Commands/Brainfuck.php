<?php

namespace App\Console\Commands;

use App\Machine;
use Illuminate\Console\Command;

class Brainfuck extends Command
{
    /**
     * @var string
     */
    protected $signature = 'brainfuck:run';

    public function handle(): int
    {
        $this->recordTime(function() {
            $code = $this->read('hello-world.b');

            $machine = new Machine(code: $code);
            $output = $machine->execute();

            $this->line($output);
        });

        return Command::SUCCESS;
    }

    protected function read(string $path): array
    {
        $path = public_path("/brainfuck/$path");
        $contents = file_get_contents($path);
        $contents = trim($contents);

        return str_split($contents);
    }

    protected function recordTime(callable $callable): void
    {
        $started = microtime(true);

        $callable();

        $taken = microtime(true) - $started;

        $microseconds = round($taken * 1000);

        $this->line("Took $microseconds microseconds");
    }
}
