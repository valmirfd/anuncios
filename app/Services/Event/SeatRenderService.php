<?php

declare(strict_types=1);

namespace App\Services\Event;

use App\Entities\Sector;
use CodeIgniter\I18n\Time;

class SeatRenderService
{

    private int|null $loggedUserId = null;

    public function __construct()
    {
        $this->loggedUserId = auth()->loggedIn() ? (int) auth()->id() : null;
    }

    public function render(array $layoutDays): string
    {
        $html = '';

        //Abertura do accordion para os dias de apresentação
        $html .= <<<ACCORDION
         <div class="accordion accordion-flush" id="event-days">          
        ACCORDION;

        foreach ($layoutDays as $day) {
            //Dia da apresentação
            $eventDateFormatted = Time::parse($day->event_date)->format('d/m/Y H:i');

            $html .= <<<ACCORDION
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#flush-collapseOne-{$day->id}" 
                                aria-expanded="false" 
                                aria-controls="flush-collapseOne-{$day->id}">
                                Apresentação {$eventDateFormatted}
                            </button>
                        </h2>
                        <div id="flush-collapseOne-{$day->id}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">                        
                                {$this->renderSections(sectors:$day->sectors, eventDayId: (int)$day->id)}                        
                            </div>
                        </div>
                    </div>
                ACCORDION;
        }

        $html .= <<<ACCORDION
         </div>          
        ACCORDION;

        return $html;
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
