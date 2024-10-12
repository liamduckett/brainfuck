<?php

namespace App;

final class Instruction
{
    public function __construct(
        public InstructionType $type,
        // represents the amount of repeated instruction type
        // except for JumpIf* in which it is used for the address of the counterpart
        public int $count,
    ) {}
}
