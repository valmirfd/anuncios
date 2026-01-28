<?php

declare(strict_types=1);

namespace App\Services\Event;

use App\Entities\Event;
use App\Entities\Row;
use App\Entities\Seat;
use App\Entities\Sector;
use App\Models\EventModel;
use App\Models\RowModel;
use App\Models\SeatModel;
use App\Models\SectorModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use DateTime;
use Exception;

class EventStoreService
{

    private EventModel $eventModel;
    private SectorModel $sectorModel;
    private array $inputRequest;
    private UploadedFile $image;

    public function __construct()
    {
        $this->eventModel = model(EventModel::class);
        $this->sectorModel = model(SectorModel::class);
        $this->inputRequest = service('request')->getPost();
        $this->image = service('request')->getFile('image');
    }

    public function create(): Event|bool
    {
        try {

            //Abrimos a transaction
            $this->eventModel->db->transBegin();

            //já faz o fill de todo o post
            $event = new Event($this->inputRequest);

            //armazenamos a imagem
            $event->image = $this->image->store('events');

            $eventId = $this->eventModel->insert($event);

            if (!$eventId) {
                throw new Exception("Erro ao criar evento");
            }

            //criamos o layout do evento
            $success = $this->storeLayout($eventId);

            if (!$success) {
                throw new Exception("Erro ao criar layout do evento");
            }

            //fechamos a transaction
            $success = $this->eventModel->db->transCommit();

            if (!$success) {
                throw new Exception("A transação para a criação do evento retornou [false]");
            }
            return $this->eventModel->find($eventId);
        } catch (\Throwable $th) {
            log_message('error', '[EVENT CREATION ERROR] {exception}', ['exception' => $th]);
            return false;
        }
    }

    private function storeLayout(int $eventId): bool
    {
        try {

            //Gerar os dias de apresentação
            $this->createEventDays(
                eventId: $eventId,
                startDate: $this->inputRequest['start_date'],
                endDate: $this->inputRequest['end_date']
            );

            //obtemos algumas variáveis do request
            $sectorNames = $this->inputRequest['sector_names'];
            $rowsCount = $this->inputRequest['rows_count'];
            $seatsCount = $this->inputRequest['seats_count'];
            $sectorTicketPrice = $this->inputRequest['sector_ticket_price'];

            foreach ($sectorNames as $index => $sectorName) {

                $fullPrice = remove_non_numeric($sectorTicketPrice[$index]);
                $discountedPrice = $fullPrice / 2; //calculamos a meia entrada

                $sectorId = $this->sectorModel->insert(
                    new Sector([
                        'event_id' => $eventId,
                        'name' => $sectorName,
                        'rows_count' => $rowsCount[$index],
                        'seats_count' => $seatsCount[$index],
                        'tickect_price' => $fullPrice,
                        'discounted_price' => $discountedPrice,
                    ])
                );

                if (!$sectorId) {
                    throw new Exception("Erro ao criar setor");
                }

                $this->createRowsAndSeats(
                    sectorId: $sectorId,
                    rowsCount: (int) $rowsCount[$index],
                    seatCount: (int) $seatsCount[$index]
                );
            } //fim foreach


            return true;
        } catch (\Throwable $th) {
            log_message('error', '[STORE LAYOUT ERROR] {exception}', ['exception' => $th]);
            return false;
        }
    }

    private function createRowsAndSeats(int $sectorId, int $rowsCount, int $seatCount): void
    {
        if ($rowsCount <= 0 || $seatCount <= 0) {
            throw new Exception("Número de filas e assentos devem ser maior que zero.");
        }

        $rowModel = model(RowModel::class);
        $seatModel = model(SeatModel::class);

        for ($row = 0; $row < $rowsCount; $row++) {
            $rowId = $rowModel->insert(
                new Row([
                    'sector_id' => $sectorId,
                    'name' => 'Fila ' . $row + 1,

                ])
            );

            if (!$rowId) {
                throw new Exception("Erro ao criar a fila do setor.");
            }

            $dataSeatsToInsert = [];

            for ($seat = 0; $seat < $seatCount; $seat++) {
                $dataSeatsToInsert[] = new Seat([
                    'row_id' => $rowId,
                    'number' => $seat + 1,

                ]);
            } //for seats

            $numberOfSeatsInserted = $seatModel->insertBatch($dataSeatsToInsert);

            if ($numberOfSeatsInserted < 1) {
                throw new Exception("Erro ao criar assentos da fila.");
            }
        } //fim do for
    }

    /**
     * Cria os dias de apresentação para o evento
     *
     * @param integer $eventId
     * @param string $startDate
     * @param string $endDate
     * @throws Exception caso as datas inválidas
     * @throws Exception caso os dias não sejam gerados
     * @return void
     */
    private function createEventDays(int $eventId, string $startDate, string $endDate): void
    {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $now = new DateTime();

        if ($start <= $now || $end <= $now) {
            throw new Exception("As datas de apresentação devem ser no futuro");
        }

        //adicionamos o dia final no intervalo
        $end->modify('+1 day');

        $days = [];

        while ($start < $end) {
            $days[] = [
                'event_id' => $eventId,
                'event_date' => $start->format('Y-m-d H:i:s')
            ];

            //avançamos para o próximo dia
            $start->modify('+1 day');
        }

        if (empty($days)) {
            throw new Exception("Não foi gerado nenhum dia de apresentação");
        }

        $result = db_connect()->table('event_days')->insertBatch($days);

        if ($result === false) {
            throw new Exception("Erro ao inserir os dias de apresentação");
        }
    }
}
