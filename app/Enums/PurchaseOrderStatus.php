<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case DRAFT = 'DRAFT';
    case APPROVED = 'APPROVED';
    case RECEIVED = 'RECEIVED';
    case CANCELLED = 'CANCELLED';

    /**
     * Determine if a transition to the target status is allowed.
     */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::DRAFT => in_array($target, [self::APPROVED, self::CANCELLED], true),
            self::APPROVED => in_array($target, [self::RECEIVED, self::CANCELLED], true),
            self::RECEIVED, self::CANCELLED => false,
        };
    }

    /**
     * Determine if the status is in a terminal state.
     */
    public function isTerminal(): bool
    {
        return match ($this) {
            self::RECEIVED, self::CANCELLED => true,
            default => false,
        };
    }
}
