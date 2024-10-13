<?php

namespace App\Console\Commands;

use App\Compiler;
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

        $instructions = $this->recordTime(function() use ($code) {
            $compiler = new Compiler($code);

            return $compiler->execute();
        });

        $this->recordTime(function() use ($instructions) {
            $machine = new Machine(instructions: $instructions , output: $this->output);

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

    protected function recordTime(callable $callable): mixed
    {
        $started = microtime(true);

        $result = $callable();

        $taken = microtime(true) - $started;

        $milliseconds = round($taken * 1000);

        $this->line("Took $milliseconds microseconds");

        return $result;
    }
}
