<?= $this->extend('Layouts/dashboard'); ?>

<?= $this->section('title'); ?>
<?= $title ?? ''; ?>
<?= $this->endSection(); ?>

<?= $this->section('css'); ?>

<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $title ?? ''; ?></h1>

    <div class="card shadow-lg mt-3">
        <div class="card-header">
            <a href="<?= route_to('dashboard.events.new') ?>" class="btn btn-success float-end ms-2"><i class="fas fa-plus me-2"></i>Novo</a>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data de início</th>
                            <th>Data de término</th>
                            <th>Criado</th>
                            <th>Atualizado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>

                            <tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justyfy-content-center">
                                            <h6 class="mb-0 text-sm"><?= $event->name; ?></h6>
                                            <p class="text-xs text-seconsary mb-0">
                                                <?= $event->code; ?>
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?= $event->start_date; ?>
                                </td>
                                <td>
                                    <?= $event->end_date; ?>
                                </td>
                                <td>
                                    <?= $event->created_at; ?>
                                </td>
                                <td>
                                    <?= $event->updated_at; ?>
                                </td>
                                <td>
                                    <a href="<?= route_to('dashboard.events.show', $event->code); ?>" class="btn btn-sm btn-dark">Detalhes</a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>

<?= $this->endSection(); ?>