<?php

namespace App;

enum InstructionType: string
{
    case IncrementCurrentData = '+';
    case DecrementCurrentData = '-';
    case IncrementDataPointer = '>';
    case DecrementDataPointer = '<';
    case ReadCharacterFromBuffer = ',';
    case WriteCharacterToBuffer = '.';
    case JumpIfZero = '[';
    case JumpIfNotZero = ']';
}
