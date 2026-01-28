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
        <div class="card-body">
            <?php if (!empty($user->stripe_account_id) && (bool) $user->stripe_account_is_completed): ?>

                <div class="form-floating">
                    <div class="alert alert-success my-3">
                        <h5 class="alert-heading">
                            Sua conta na Stripe está criada e pronta para receber os repasses.
                            <br>
                            <a href="<?= route_to('dashboard.events') ?>" class="btn btn-sm btn-primary mb-3 mt-3">Criar meus eventos</a>
                            <a target="_blank" href="<?= route_to('dashboard.organizer.panel') ?>" class="btn btn-sm btn-dark">Acessar minha conta na Stripe</a>
                        </h5>
                    </div>
                </div>
            <?php else: ?>
                <?= form_open(
                    action: route_to('dashboard.organizer.create.account'),
                    attributes: ['id' => 'form']
                ); ?>

                <?php if (empty($user->stripe_account_id)): ?>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <button type="submit" id="btnSubmit" class="btn btn-dark">Criar minha conta de organizador na Stripe</button>
                        </div>
                    </div>
                <?php endif; ?>

                <?= form_close(); ?>
            <?php endif; ?>

            <?php if (!empty($user->stripe_account_id) && !(bool) $user->stripe_account_is_completed): ?>

                <div class="alert alert-warning my-3">
                    Sua conta está criada, mas ainda tem pendências que precisam da sua atenção.
                    Caso já tenha resolvido, aguarde uns instantes e clique no botão abaixo para verificar a situação.
                </div>

                <?= form_open(
                    action: route_to('dashboard.organizer.check.account'),
                    attributes: ['id' => 'form'],
                    hidden: ['_method' => 'PUT']
                ); ?>

                <div class="row">
                    <div class="col-md-12 mt-3">
                        <button type="submit" id="btnCheck" class="btn btn-danger">Verificar minha conta</button>
                    </div>
                </div>

                <?= form_close(); ?>
            <?php endif; ?>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>

<?= $this->section('js'); ?>

<?= $this->endSection(); ?>