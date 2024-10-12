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
        $code = str_split(
            '++++++++[>++++[>++>+++>+++>+<<<<-]>+>+>->>+[<]<-]>>.>---.+++++++..+++.>>.<-.<.+++.------.--------.>>+.>++.'
        );

        $machine = new Machine(code: $code);

        $output = $machine->execute();

        $this->line($output);

        return Command::SUCCESS;
    }
}
