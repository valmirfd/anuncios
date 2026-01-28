<?php

namespace App\Models;

use App\Entities\Row;
use App\Models\Basic\AppModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class RowModel extends AppModel
{


    protected $table            = 'rows';
    protected $returnType       = Row::class;
    protected $allowedFields    = [
        'sector_id',
        'name',
    ];

    protected $useTimestamps = false;
}
