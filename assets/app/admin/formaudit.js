$(function () {

    var formulir = $('#formulir').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,5], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/formaudit/listformulir",
            "type": "POST"
        }
    });

   

    $("#formulir").on("click", ".edit", function () {
        var id = $(this).attr('id');

        $.ajax({
            url: base_url + "/formaudit/getFormulirById/"+id,
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (list) {
                swal.close();
                if (list.status) {
                    $('#formulirEditModal').modal('show');
                    $("#edit_form_nama").val(list.form_nama);
                    $("#edit_form_kode").val(list.form_kode);
                    $("#form_id").val(list.form_id);
                    $('#edit_isi_artikel').summernote('code',list.form_deskripsi);
                    //tinymce.get('edit_isi_artikel').setContent(list.form_deskripsi);
                    
                } else {
                    swal.fire("Oops", "Gagal!", "error");
                        $("#formeditformulir")
                    .formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
                }   
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
                
                 $("#formeditformulir")
                .formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
            }
        });
    });


    $("#formulir").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapus(id);
    });

    $("#formulir").on("click", ".detail", function () {
        var id = $(this).attr('id');
        location.href = base_url + "/detailform?id="+id;
    });

    function hapus($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Hapus Formulir Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/formaudit/hapus",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Formulir Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    formulir.ajax.reload();
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    $("#tambah").on("click", function () {
        $("#formulirAddModal").modal('show');
    });


    $("#formaddformulir").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {
            clazz: 'invalid-feedback'
        },
        control: {
            valid: 'is-valid',
            invalid: 'is-invalid'
        },
        row: {
            invalid: 'has-danger'
        }
    }).on('success.form.fv', function(e) {
        e.preventDefault();

        var $form = $(e.target);       // ✅ perbaikan
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/formaudit/tambah",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#formulirAddModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        formulir.ajax.reload();
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

    return false;
});


$("#formeditformulir").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled'],
        err: {
            clazz: 'invalid-feedback'
        },
        control: {
            valid: 'is-valid',
            invalid: 'is-invalid'
        },
        row: {
            invalid: 'has-danger'
        }
    }).on('success.form.fv', function(e) {
        e.preventDefault();

        var $form = $(e.target);       // ✅ perbaikan
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/formaudit/edit",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#formulirEditModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        formulir.ajax.reload();
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

    return false;
});


  
})