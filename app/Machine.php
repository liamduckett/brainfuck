<?php

namespace App;

final class Machine
{
    protected int $instructionPointer;
    protected array $memory;
    protected int $dataPointer;
    protected mixed $output;
    protected string $buffer;

    public function __construct(
        protected array $code,
    ) {
        $this->instructionPointer = 0;
        $this->memory = array_fill(0, 30_000, 0);
        $this->dataPointer = 0;
        $this->output = '';
        $this->buffer = '';
    }

    public function execute(): string
    {
        while($this->instructionPointer < count($this->code)) {
            $instruction = $this->code[$this->instructionPointer];

            match($instruction) {
                '+' => $this->memory[$this->dataPointer] += 1,
                '-' => $this->memory[$this->dataPointer] -= 1,
                '>' => $this->dataPointer += 1,
                '<' => $this->dataPointer -= 1,
                ',' => $this->memory[$this->dataPointer] = $this->buffer,
                '.' => $this->putCharacter(),
                '[' => $this->leftBracket(),
                ']' => $this->rightBracket(),
            };

            $this->instructionPointer += 1;
        }

        return $this->output;
    }

    // Internals

    protected function putCharacter(): void
    {
        $this->buffer = $this->memory[$this->dataPointer];
        $this->output .= chr($this->memory[$this->dataPointer]);
    }

    protected function leftBracket(): void
    {
        if($this->memory[$this->dataPointer] === 0) {
            $depth = 1;

            while($depth !== 0) {
                $this->instructionPointer += 1;

                $instruction = $this->code[$this->instructionPointer];

                match($instruction) {
                    '[' => $depth += 1,
                    ']' => $depth -= 1,
                    default => null,
                };
            }
        }
    }

    protected function rightBracket(): void
    {
        if($this->memory[$this->dataPointer] !== 0) {
            $depth = 1;

            while($depth !== 0) {
                $this->instructionPointer -= 1;

                $instruction = $this->code[$this->instructionPointer];

                match($instruction) {
                    ']' => $depth += 1,
                    '[' => $depth -= 1,
                    default => null,
                };
            }
        }
    }
}
