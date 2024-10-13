<?php

namespace App;

use Illuminate\Console\OutputStyle;

final class Machine
{
    protected int $instructionPointer;
    protected array $memory;
    protected int $dataPointer;
    protected string $buffer;

    public function __construct(
        protected readonly array $instructions,
        protected OutputStyle $output,
    ) {
        $this->instructionPointer = 0;
        $this->memory = array_fill(0, 30_000, 0);
        $this->dataPointer = 0;
        $this->buffer = '';
    }

    public function execute(): void
    {
        while($this->instructionPointer < count($this->instructions)) {
            $instruction = $this->currentInstruction();

            match($instruction->type) {
                InstructionType::IncrementCurrentData => $this->incrementCurrentData($instruction),
                InstructionType::DecrementCurrentData => $this->decrementCurrentData($instruction),
                InstructionType::IncrementDataPointer => $this->incrementDataPointer($instruction),
                InstructionType::DecrementDataPointer => $this->decrementDataPointer($instruction),
                InstructionType::ReadCharacterFromBuffer => $this->readCharacterFromBuffer($instruction),
                InstructionType::WriteCharacterToBuffer => $this->writeCharacterToBuffer($instruction),
                InstructionType::JumpIfZero => $this->jumpIfZero($instruction),
                InstructionType::JumpIfNotZero => $this->jumpIfNotZero($instruction),
            };

            $this->incrementInstructionPointer();
        }
    }

    // Internals

    protected function readCharacterFromBuffer(Instruction $instruction): void
    {
        foreach(range(1, $instruction->count) as $_) {
            $this->memory[$this->dataPointer] = $this->buffer;
        }
    }

    protected function writeCharacterToBuffer(Instruction $instruction): void
    {
        foreach(range(1, $instruction->count) as $_) {
            $this->buffer = $this->currentData();

            $character = chr($this->buffer);

            $this->output->write($character);
        }
    }

    protected function jumpIfZero(Instruction $instruction): void
    {
        // MAKE THIS USE ARG

        if($this->currentData() === 0) {
            $this->instructionPointer = $instruction->count;
        }
    }

    protected function jumpIfNotZero(Instruction $instruction): void
    {
        if($this->currentData() !== 0) {
            $this->instructionPointer = $instruction->count;
        }
    }

    // Deep Internals

    protected function currentInstruction(): Instruction
    {
        return $this->instructions[$this->instructionPointer];
    }

    protected function currentData(): int
    {
        return $this->memory[$this->dataPointer];
    }

    protected function incrementCurrentData(Instruction $instruction): void
    {
        $this->memory[$this->dataPointer] += $instruction->count;
    }

    protected function decrementCurrentData(Instruction $instruction): void
    {
        $this->memory[$this->dataPointer] -= $instruction->count;
    }

    protected function incrementDataPointer(Instruction $instruction): void
    {
        $this->dataPointer += $instruction->count;
    }

    protected function decrementDataPointer(Instruction $instruction): void
    {
        $this->dataPointer -= $instruction->count;
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
