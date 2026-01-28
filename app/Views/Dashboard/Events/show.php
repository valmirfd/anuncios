<?php echo $this->extend('Layouts/dashboard'); ?>

<?php echo $this->section('title'); ?>
<?php echo $title; ?>
<?php echo $this->endSection(); ?>

<?php echo $this->section('css'); ?>

<style>
    .event-image-detail {

        width: 100%;
        max-width: 500px;
        display: block;
        margin: 0 auto;

    }
</style>

<?php echo $this->endSection(); ?>

<?php echo $this->section('content'); ?>


<div class="container-fluid">

    <div class="card mt-5 shadow-lg">
        <div class="card-header">
            <h5 class="card-title mb-2"><?php echo $title; ?></h5>
            <a href="<?php echo route_to('dashboard.events'); ?>"
                class="btn btn-outline-secondary">
                <i class="fas fa-angle-double-left"></i>
                Listar eventos
            </a>
            <a href="<?php echo route_to('dashboard.events.new'); ?>"
                class="btn btn-outline-success ms-2">
                <i class="fas fa-plus"></i>
                Novo evento
            </a>

        </div>
        <div class="card-body">

            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Dados do evento</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Dias de apresentação</button>
                </li>

            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Evento: <?= $event->name; ?></h5>
                            <p>Data de início: <?= $event->start_date; ?></p>
                            <p>Data de término: <?= $event->end_date; ?></p>
                            <p>Local do evento: <?= $event->location; ?></p>
                            <p>Descrição do evento: <?= $event->description; ?></p>
                        </div>
                        <div class="col-md-6 text-center">
                            <h5>Imagem</h5>
                            <?= $event->image(); ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                    Dias de apresentação
                </div>

            </div>

        </div>
    </div>

</div>

<?php echo $this->endSection(); ?>


<?php echo $this->section('js'); ?>




<?php echo $this->endSection(); ?>