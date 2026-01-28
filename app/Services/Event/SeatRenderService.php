<?php

declare(strict_types=1);

namespace App\Services\Event;

use App\Entities\Sector;

class SeatRenderService
{

    private int|null $loggedUserId = null;

    public function __construct()
    {
        $this->loggedUserId = auth()->loggedIn() ? (int) auth()->id() : null;
    }

    public function render(array $layoutDays): string
    {

        return '';
    }



    private function renderSections(array $sectors, int $eventDayId): string
    {

        return '';
    }

    public function renderSeatsForSector(
        array $rows,
        int $sectorId,
        string $sectorName,
        int $eventDayId
    ): string {

        return '';
    }

    private function calculateSeatsTotal(Sector $sector): array
    {

        return ['totals' => array_reduce($sector->rows, fn($carry, $row) => $carry + count($row->seats), 0)];
    }
}
