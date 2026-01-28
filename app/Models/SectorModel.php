<?php

namespace App\Models;

use App\Entities\Sector;
use App\Models\Basic\AppModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class SectorModel extends AppModel
{


    protected $table            = 'sectors';
    protected $returnType       = Sector::class;
    protected $allowedFields    = [
        'event_id',
        'name',
        'ticket_price',
        'discounted_price',
        'rows_count',
        'seats_count',
    ];

    protected $useTimestamps = false;
}
