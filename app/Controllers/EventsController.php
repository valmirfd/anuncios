<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Event;
use App\Models\EventModel;
use App\Validation\EventValidation;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;

class EventsController extends BaseController
{
    use ResponseTrait;

    private EventModel $model;

    public function __construct()
    {
        $this->model = model(EventModel::class);
    }

    public function index()
    {
        $data = [
            'title' => 'Meus eventos',
            'events' => $this->model->whereUser()->orderBy('name', 'ASC')->findAll()
        ];

        return view('Dashboard/Events/index', $data);
    }
}
