<?php

namespace App;

class Compiler
{
    protected array $code;
    protected int $codePointer = 0;

    protected array $instructions;

    protected array $loopStack = [];

    public function __construct(
        array $code,
    ) {
        $this->code = array_map(
            fn(string $instruction) => InstructionType::from($instruction),
            $code,
        );
    }

    public function execute(): array
    {
        while($this->codePointer < count($this->code)) {
            $instructionType = $this->code[$this->codePointer];

            match($instructionType) {
                InstructionType::IncrementCurrentData => $this->compileFoldableInstruction($instructionType),
                InstructionType::DecrementCurrentData => $this->compileFoldableInstruction($instructionType),
                InstructionType::IncrementDataPointer => $this->compileFoldableInstruction($instructionType),
                InstructionType::DecrementDataPointer => $this->compileFoldableInstruction($instructionType),
                InstructionType::ReadCharacterFromBuffer => $this->compileFoldableInstruction($instructionType),
                InstructionType::WriteCharacterToBuffer => $this->compileFoldableInstruction($instructionType),
                InstructionType::JumpIfZero => $this->compileJumpIfZero($instructionType),
                InstructionType::JumpIfNotZero => $this->compileJumpIfNotZero($instructionType),
            };

            $this->codePointer += 1;
        }

        return $this->instructions;
    }

    protected function compileFoldableInstruction(InstructionType $instructionType): void
    {
        $count = 1;

        while($this->codePointer < count($this->code) - 1
            && $this->code[$this->codePointer + 1] === $instructionType) {
            $count += 1;
            $this->codePointer += 1;
        }

        $this->emitWithCount($instructionType, $count);
    }

    protected function compileJumpIfZero(InstructionType $instructionType): void
    {
        $position = $this->emitWithCount($instructionType, 0);

        $this->loopStack[] = $position;
    }

    protected function compileJumpIfNotZero(InstructionType $instructionType): void
    {
        $lastJumpZero = array_pop($this->loopStack);
        $position = $this->emitWithCount($instructionType, $lastJumpZero);

        $this->instructions[$lastJumpZero]->count = $position;
    }

    protected function emitWithCount(InstructionType $instructionType, int $count): int
    {
        $instruction = new Instruction(
            type: $instructionType,
            count: $count,
        );

        $this->instructions[] = $instruction;

        return count($this->instructions) - 1;
    }
}
