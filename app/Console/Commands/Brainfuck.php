<?php

namespace App\Console\Commands;

use App\Machine;
use Illuminate\Console\Command;

class Brainfuck extends Command
{
    /**
     * @var string
     */
    protected $signature = 'brainfuck:run {file}';

    public function handle(): int
    {
        $file = $this->argument('file');
        $code = $this->read($file);

        $this->recordTime(function() use ($code) {
            $machine = new Machine(code: $code , output: $this->output);

            $machine->execute();
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
