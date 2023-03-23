<h2 class="mb-3"><?= $title; ?></h2>

<?php if (session('errors')) { ?>
    <?= $this->include("components/alert_error.php") ?>
<?php } ?>

<?php if (session('success')) { ?>
    <?= $this->include("components/alert_success.php") ?>
<?php } ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive" id="table">
                    <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrfToken" />
                    <table id="datatable" class="table table-borderless table-hover" style="width: 100% !important;">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col"><?= lang('App.date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>