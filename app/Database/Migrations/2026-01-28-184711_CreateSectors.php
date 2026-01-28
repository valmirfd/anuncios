<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSectors extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'event_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                ],
                'rows_count' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'comment'        => 'Número de filas',
                ],
                'seats_count' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'comment'        => 'Número de assentos por fila',
                ],
                'name' => [
                    'type'           => 'VARCHAR',
                    'constraint'     => 128,
                    'null'           => false,
                ],
                'ticket_price' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'comment'        => 'Preço da entrada integral em centavos',
                ],
                'discounted_price' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'comment'        => 'Preço da meia-entrada em centavos',
                ],
            ]
        );

        $this->forge->addKey('id', true);
        $this->forge->addKey('event_id');
        $this->forge->addKey('name');
        $this->forge->addKey('ticket_price');
        $this->forge->addKey('discounted_price');

        $this->forge->addForeignKey('event_id', 'events', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('sectors', attributes: ['comment' => 'Tabela de setores dos eventos']);
    }

    public function down()
    {
        $this->forge->dropTable('sectors');
    }
}
