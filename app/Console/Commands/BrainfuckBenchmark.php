<?php

namespace App\Console\Commands;

use App\Compiler;
use App\Machine;
use Illuminate\Console\Command;

class BrainfuckBenchmark extends Command
{
    /**
     * @var string
     */
    protected $signature = 'brainfuck:run {file}';

    public function handle(): int
    {
        $file = $this->argument('file');
        $code = $this->read($file);

        [$instructions] = $this->recordTime(function() use ($code) {
            $compiler = new Compiler($code);

            return $compiler->execute();
        });

        $times = [];

        foreach(range(1, 10) as $count) {
            [$_, $time] = $this->recordTime(function() use ($instructions) {
                $machine = new Machine(instructions: $instructions , output: $this->output);

                return $machine->execute();
            });

            $times[] = $time;
            $this->line("$count. $time ms");
        }

        $averageTime = array_sum($times) / 10;

        $this->line('---');
        $this->line("Average: $averageTime ms");

        return Command::SUCCESS;
    }

    protected function read(string $path): array
    {
        $path = public_path("/brainfuck/$path");
        $contents = file_get_contents($path);
        $contents = trim($contents);

        return str_split($contents);
    }

    protected function recordTime(callable $callable): array
    {
        $started = microtime(true);

        $result = $callable();

        $taken = microtime(true) - $started;

        $milliseconds = round($taken * 1000);

        return [$result, $milliseconds];
    }
}
