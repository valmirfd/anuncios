<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Event extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [];

    public function image(string $class = 'rounded-3 event-image-detail'): string
    {
        if (empty($this->image)) {
            return 'Sem imagem';
        }

        $path = WRITEPATH . "uploads/{$this->image}";

        if (!file_exists($path)) {
            return 'Imagem não encontrada';
        }

        $data = file_get_contents($path);
        $imageContentBase64 = 'data:image/png;base64,' . base64_encode($data);

        return img(
            src: $imageContentBase64,
            attributes: ['class' => $class]
        );
    }
}
