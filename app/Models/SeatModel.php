<?php

namespace App\Models;

use App\Entities\Seat;
use App\Models\Basic\AppModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class SeatModel extends AppModel
{


    protected $table            = 'seats';
    protected $returnType       = Seat::class;
    protected $allowedFields    = [
        'row_id',
        'number',
    ];

    protected $useTimestamps = false;
}
