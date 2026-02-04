<?php

declare(strict_types=1);

namespace App\Services\Event;

use App\Entities\SeatBooking;
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
                                <strong>Preço integral:</strong> {$sector->ticketPrice()} <br>              
                                <strong>Preço meia entrada:</strong> {$sector->discountedPrice()} <br>             
                                <strong>Total de assentos:</strong> {$totals['total']}       
                                
                                <div class="table-responsive mb-3">
                                    <table class="table table-boderless table-sm sector-table">
                                        <thead>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th class="text-center"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {$seatsHTML}
                                        </tbody>
                                    </table>
                                </div>
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
        $seatsHTML = '';

        //Obtemos as iniciais do setor
        $sectorInitials = implode('', array_map(fn($word) => strtoupper($word[0]), explode(' ', $sector->name)));

        //Percorremos as filas do setor
        foreach ($sector->rows as $row) {
            $rowHTML = <<<TABLE

                <td><strong>{$row->name}</strong></td>

            TABLE;

            //Preciso percorrer os asseentos de cada fila
            foreach ($row->seats as $seat) {
                //Geração do código único do assento, baseado no dia do evento, setor, fileira e número do assento
                $seatCode = base64_encode("{$eventDayId}-{$sector->id}-{$row->id}-{$seat->number}");

                //Obtem as reservas do assento para o dia do evento, caso existam
                $dayBookings = $seat->bookings[$eventDayId] ?? [];

                //Define valores padrão para o botão do assento (assento disponível)
                $btnClass = 'btn-dark btn-seat'; // classes css padrão
                $title = 'Disponível'; //Título para o hover padrão
                $seatCodeAttibute = "data-seat={$seatCode}"; // Atributo único do assento

                //Percorremos as reservas para o assento, caso existam

                /** @var SeatBooking $booking */
                foreach ($dayBookings as $booking) {
                    if ($booking->isSold() || $booking->isPending()) {
                        $btnClass = 'btn-danger';
                        $title = $booking->status();
                        $seatCodeAttibute = ''; //remove o atributo - data-seat
                        break;
                    }
                }
            }


            //Fechamento do TABLE
            $seatsHTML .= <<<TABLE
                  <tr>{$rowHTML}</tr>
            TABLE;
        }




        return $seatsHTML;
    }

    private function calculateSeatsTotal(Sector $sector): array
    {

        return ['total' => array_reduce($sector->rows, fn($carry, $row) => $carry + count($row->seats), 0)];
    }
}
