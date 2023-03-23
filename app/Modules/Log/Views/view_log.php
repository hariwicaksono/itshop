<?= $this->extend('layouts/app_dashboard.php') ?>

<!-- Content -->
<?= $this->section('content') ?>
<div class="viewData">
    <?= $this->include('App\Modules\Log\Views\data_log'); ?>
</div>

<?= $this->endSection('content') ?>

<?= $this->section('modal'); ?>
<div class="viewModal"></div>
<?= $this->endSection(); ?>

<?= $this->section('script') ?>
<script>
    $(document).ready(function() {
        show_data();
    })

    function show_data() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        var table = $('#datatable').on('draw.dt', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }).DataTable({
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            oLanguage: {
                "sLengthMenu": "<?= lang('App.show'); ?> _MENU_ data <?= lang('App.perPage'); ?>",
                "sInfo": "<?= lang('App.showing'); ?> _START_ <?= lang('App.up/to'); ?> _END_ <?= lang('App.from'); ?> _TOTAL_ data",
                "sInfoEmpty": "<?= lang('App.showing'); ?> 0 <?= lang('App.up/to'); ?> 0 <?= lang('App.from'); ?> 0 data",
                "sZeroRecords": "<?= lang('App.noDataFound'); ?>",
                "sInfoFiltered": "(<?= lang('App.filtered'); ?> <?= lang('App.from'); ?> _MAX_ total data)",
            },
            'destroy': true,
            'responsive': true,
            'processing': true,
            'serverSide': true,
            'order': [],
            'ajax': {
                'url': '<?= base_url('log/listdata'); ?>',
                'type': 'POST',
                "data": {
                    <?= csrf_token() ?>: $('input[name=<?= csrf_token() ?>]').val(),
                },
                "data": function(data) {
                    data.<?= csrf_token() ?> = $('input[name=<?= csrf_token() ?>]').val()
                },
                "dataSrc": function(response) {
                    $('input[name=<?= csrf_token() ?>]').val(response.<?= csrf_token() ?>);
                    return response.data;
                },
            },
            'columnDefs': [{
                'targets': [-1], //sesuaikan kolom yang tidak mau di sort
                'orderable': false
            }, ],
        })

        $('.tooltip').not(this).tooltip('hide');

        $('.btnAdd').click(function() {
            $.ajax({
                url: '<?= base_url('akun/create'); ?>',
                dataType: 'json',
                beforeSend: function() {
                    $('#add').html('<i class="mdi mdi-loading mdi-spin"></i> <span><?= lang('App.add'); ?> Data</span>');
                },
                complete: function() {
                    $('#add').html('<i class="mdi mdi-plus"></i> <span><?= lang('App.add'); ?> Data</span>');
                },
                success: function(response) {
                    $('.viewModal').html(response.data);
                    $('#addModal').modal({
                        backdrop: "static"
                    });
                    $('#addModal').modal('show');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            })
        })
    }

    function edit_data(id) {
        var csrfName = '<?= csrf_token() ?>'; // Value specified in $config
        var csrfHash = $('input[name=<?= csrf_token() ?>]').val(); // CSRF hash
        $.ajax({
            url: '<?= base_url('akun/edit'); ?>',
            type: 'POST',
            data: {
                id: id,
                [csrfName]: csrfHash
            },
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token) {
                    $('#csrfToken, input[name=<?= csrf_token() ?>]').val(response.csrf_token);
                }
                $('.viewModal').html(response.data);
                $('#editModal').modal({
                    backdrop: "static"
                });
                $('#editModal').modal('show');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        })
    }

    function delete_data(id) {
        Swal.fire({
            icon: 'warning',
            title: '<?= lang('App.areYouSure'); ?>',
            text: "<?= lang('App.deleted'); ?>!",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<?= lang('App.yesDelete'); ?>!',
            cancelButtonText: '<?= lang('App.cancel'); ?>',
        }).then((result) => {
            if (result.isConfirmed) {
                var csrfName = '<?= csrf_token() ?>'; // Value specified in $config
                var csrfHash = $('input[name=<?= csrf_token() ?>]').val(); // CSRF hash
                $.ajax({
                    url: '<?= base_url('akun/delete'); ?>',
                    type: 'post',
                    data: {
                        id: id,
                        [csrfName]: csrfHash
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.csrf_token) {
                            $('#csrfToken, input[name=<?= csrf_token() ?>]').val(response.csrf_token);
                        }

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            confirmButtonColor: '#3085d6',
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            },
                        })
                        Toast.fire({
                            icon: 'success',
                            title: '<?= lang('App.delSuccess'); ?>!'
                        })

                        $('.viewData').html(response.data);
                        show_data();
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                })
            }
        })
    }

    function refresh_data() {
        $.ajax({
            url: '<?= base_url('akun/refresh'); ?>',
            type: 'GET',
            cache: false,
            dataType: 'json',
            success: function(response) {
                if (response.csrf_token) {
                    $('#csrfToken, input[name=<?= csrf_token() ?>]').val(response.csrf_token);
                }
                //window.location.reload(true);
                $('.viewData').html(response.data);
                show_data();

            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        })
    }
</script>
<?= $this->endSection('script') ?>