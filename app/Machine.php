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
        protected readonly array $code,
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
            match($this->currentInstruction()) {
                '+' => $this->incrementCurrentData(),
                '-' => $this->decrementCurrentData(),
                '>' => $this->incrementDataPointer(),
                '<' => $this->decrementDataPointer(),
                ',' => $this->readCharacterFromBuffer(),
                '.' => $this->writeCharacterToBuffer(),
                '[' => $this->leftBracket(),
                ']' => $this->rightBracket(),
            };

            $this->incrementInstructionPointer();
        }

        return $this->output;
    }

    // Internals

    protected function readCharacterFromBuffer(): void
    {
        $this->memory[$this->dataPointer] = $this->buffer;
    }

    protected function writeCharacterToBuffer(): void
    {
        $this->buffer = $this->currentData();
        $this->output .= chr($this->buffer);
    }

    protected function leftBracket(): void
    {
        if($this->currentData() === 0) {
            $depth = 1;

            while($depth !== 0) {
                $this->incrementInstructionPointer();

                match($this->currentInstruction()) {
                    '[' => $depth += 1,
                    ']' => $depth -= 1,
                    default => null,
                };
            }
        }
    }

    protected function rightBracket(): void
    {
        if($this->currentData() !== 0) {
            $depth = 1;

            while($depth !== 0) {
                $this->decrementInstructionPointer();

                match($this->currentInstruction()) {
                    ']' => $depth += 1,
                    '[' => $depth -= 1,
                    default => null,
                };
            }
        }
    }

    // Deep Internals

    protected function currentInstruction(): string
    {
        return $this->code[$this->instructionPointer];
    }

    protected function currentData(): int
    {
        return $this->memory[$this->dataPointer];
    }

    protected function incrementCurrentData(): void
    {
        $this->memory[$this->dataPointer] += 1;
    }

    protected function decrementCurrentData(): void
    {
        $this->memory[$this->dataPointer] -= 1;
    }

    protected function incrementDataPointer(): void
    {
        $this->dataPointer += 1;
    }

    protected function decrementDataPointer(): void
    {
        $this->dataPointer -= 1;
    }

    protected function incrementInstructionPointer(): void
    {
        $this->instructionPointer += 1;
    }

    protected function decrementInstructionPointer(): void
    {
        $this->instructionPointer -= 1;
    }
}
