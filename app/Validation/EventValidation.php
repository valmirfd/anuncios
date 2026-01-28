<?php

namespace App\Validation;

class EventValidation
{
    public function getRules(): array
    {
        return [
            'name' => [
                'rules' => [
                    'required',
                    'max_length[255]'
                ],
                'errors' => [
                    'required' => 'O nome do evento é obrigatório',
                    'max_length' => 'O nome deve ter no máximo 255 caractéres',
                ],
            ],
            'location' => [
                'rules' => [
                    'required',
                ],
            ],
            'start_date' => [
                'rules' => [
                    'required',
                ],
            ],
            'end_date' => [
                'rules' => [
                    'required',
                ],
            ],
            'description' => [
                'rules' => [
                    'required',
                ],
            ],
            'image' => [
                'rules' => [
                    'uploaded[image]',
                    'ext_in[image,png,jpg,webp]',
                    'max_size[image,2048]',
                ],
            ],
        ];
    }
}
