<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PIIStatus extends Enum
{
    const PENDING = 'pending';
    const PASSED = 'pass';
    const FAILED = 'fail';
}
