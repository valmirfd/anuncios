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
        $html = '';

        //Abertura do accordion para os sectors
        $html .= <<<ACCORDION
         <div class="accordion accordion-flush" id="sectors">          
        ACCORDION;

        foreach ($sectors as $sector) {

            //Obtemos os totais dos assentos para o setor atual
            $totals = $this->calculateSeatsTotal($sector);

            $seatsHTML = $this->renderSeatsForSector(sector: $sector, eventDayId: $eventDayId);

            $html .= <<<ACCORDION
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#flush-collapseOne-{$eventDayId}-{$sector->id}" 
                                aria-expanded="false" 
                                aria-controls="flush-collapseOne-{$eventDayId}-{$sector->id}">
                                Setor {$sector->name}
                            </button>
                        </h2>
                        <div id="flush-collapseOne-{$eventDayId}-{$sector->id}" class="accordion-collapse collapse" data-bs-parent="#sectors">
                            <div class="accordion-body">                        
                                <strong>Preço integral:</strong> {$sector->ticketPrice()}                  
                                <strong>Preço meia entrada:</strong> {$sector->discountedPrice()}                  
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

    public function renderSeatsForSector(Sector $sector, int $eventDayId): string
    {

        return '';
    }

    private function calculateSeatsTotal(Sector $sector): array
    {

        return ['totals' => array_reduce($sector->rows, fn($carry, $row) => $carry + count($row->seats), 0)];
    }
}
