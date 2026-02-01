<?php

declare(strict_types=1);

namespace App\Services\Event;

use App\Models\RowModel;
use App\Models\SeatBookingModel;
use App\Models\SeatModel;
use App\Models\SectorModel;
use Exception;

class EventLayoutService
{

    public function build(int $eventId): array
    {
        //buscamos os dias do evento
        $eventDays = db_connect()->table('event_days')
            ->where('event_id', $eventId)
            ->orderBy('event_date', 'ASC')
            ->get()
            ->getResult();


        if (empty($eventDays)) {
            throw new Exception("O evento ID: {$eventId} não tem dias de apresentação");
        }

        //buscamos os setores do evento
        $sectors = model(SectorModel::class)
            ->where('event_id', $eventId)
            ->orderBy('name', 'ASC')
            ->findAll();

        //para cada setor buscamos as suas filas
        $rows = empty($sectors) ? [] : model(RowModel::class)
            ->whereIn('sector_id', array_column($sectors, 'id'))
            ->orderBy('id', 'ASC')
            ->findAll();

        //para cada fila buscamos os seus assentos
        $seats = empty($rows) ? [] : model(SeatModel::class)
            ->whereIn('row_id', array_column($rows, 'id'))
            ->orderBy('id', 'ASC')
            ->findAll();

        //recuperamos todas as reservas de assentos associadas aos dias do evento
        $bookingsDays = empty($seats) ? [] : model(SeatBookingModel::class)
            ->whereIn('event_day_id', array_column($eventDays, 'id'))
            //recuperamos todos sem restrição se estão expirados ou não
            //pois queremos esses dados para exibir no front e definir como o botão do assento
            //será exibido.
            ->findAll();

        //criamos um array temporário para armazenar os bookings Agrupados por assento e dia do evento
        $tempBookings = [];

        //Percorremos as reservas associadas aos dias do evento
        foreach ($bookingsDays as $booking) {

            $seatId = (int) $booking->seat_id;
            $eventDayId = (int) $booking->event_day_id;

            $tempBookings[$seatId][$eventDayId][] = $booking;
        }

        //para cada assento, clonamos e adicionamos os bookings correspondentes ao dia do evento
        $seatsWithBookings = [];

        //Agora percorremos os assentos
        foreach ($seats as $seat) {
            $seatCopy = clone $seat;
            $auxBookings = [];

            //Percorremos os dias do evento
            foreach ($eventDays as $eventDay) {
                $eventDayId = (int) $eventDay->id;

                $auxBookings[$eventDayId] = $tempBookings[$seat->id][$eventDayId] ?? [];
            }

            //Agora atribuo ao seatCopy as reservas correspondentes
            $seatCopy->bookings = $auxBookings;

            //Agora adicionamos o assento clonado ao array de assentos com reservas
            $seatsWithBookings[] = $seatCopy;
        }

        //Agora para cada fila associamos os assentos que a essa altura já estão com os dados de reservas definidos
        foreach ($rows as $row) {
            $row->seats = array_filter($seatsWithBookings, function ($seat) use ($row) {
                return (int) $seat->row_id === (int) $row->id;
            });
        }

        //Agora para cada setor associamos oa filas que a essa altura já estão com os dados de assentos definidos
        foreach ($sectors as $sector) {
            $sector->rows = array_filter($rows, function ($row) use ($sector) {
                return (int) $row->sector_id === (int) $sector->id;
            });
        }

        //Para cada dia do evento, associamos os setores que já estão com as filas e assentos
        foreach ($eventDays as $day) {
            $day->sectors = $sectors;
        }

        return $eventDays;
    }
}
