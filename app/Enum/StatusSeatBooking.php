<?php

declare(strict_types=1);

namespace App\Enum;

enum StatusSeatBooking: string
{
    case Reserved = 'reserved';
    case Sold = 'sold';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Reserved => 'Reservado',
            self::Sold => 'Vendido',
            self::Pending => 'Aguardando pagamento',
        };
    }

    public function isReserved(): bool
    {
        return $this->status === StatusSeatBooking::Reserved->value;
    }

    public function isSold(): bool
    {
        return $this->status === StatusSeatBooking::Sold->value;
    }

    public function isPending(): bool
    {
        return $this->status === StatusSeatBooking::Pending->value;
    }
}
