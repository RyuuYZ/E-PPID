<?php

namespace App\Exceptions;

use App\Enums\PermohonanStatus;
use RuntimeException;

class InvalidStatusTransitionException extends RuntimeException
{
    public function __construct(
        public readonly PermohonanStatus $from,
        public readonly PermohonanStatus $to,
    ) {
        $allowedLabels = array_map(fn($s) => $s->label(), $from->allowedTransitions());
        $allowedStr = empty($allowedLabels) ? '(tidak ada — status terminal)' : implode(', ', $allowedLabels);

        parent::__construct(
            "Transisi status tidak valid: [{$from->label()}] → [{$to->label()}]. " .
            "Transisi yang diizinkan dari [{$from->label()}]: {$allowedStr}."
        );
    }
}
