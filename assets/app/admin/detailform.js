$(function () {

    var dtform = $('#dtform').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,4], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/detailform/listdtform/"+form_id,
            "type": "POST"
        }
    });

   

    $("#dtform").on("click", ".edit", function () {
        var id = $(this).attr('id');

        $.ajax({
            url: base_url + "/detailform/getdtformById/"+id,
            type: "GET",
            dataType: "json",
            beforeSend: function () {
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (list) {
                swal.close();
                if (list.status) {
                    $('#dtformEditModal').modal('show');
                    $("#edit_dtform_tujuan").val(list.dtform_tujuan);
                    $("#dtform_id").val(list.dtform_id);
                    tinymce.get('edit_dtform_pertanyaan').setContent(list.dtform_pertanyaan);
                    tinymce.get('edit_dtform_lingkup').setContent(list.dtform_lingkup);
                    
                } else {
                    swal.fire("Oops", "Gagal!", "error");
                        $("#formeditdtform")
                    .formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
                }   
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
                
                 $("#formeditdtform")
                .formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
            }
        });
    });


    $("#dtform").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapus(id);
    });

    $("#dtform").on("click", ".detail", function () {
        var id = $(this).attr('id');
        location.href = base_url + "/detailform?id="+id;
    });

    function hapus($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Hapus dtform Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/detailform/hapus",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "dtform Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    dtform.ajax.reload();
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
        $("#dtformAddModal").modal('show');
    });


    $("#formadddtform").formValidation({
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
            url: base_url + "/detailform/tambah",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#dtformAddModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        dtform.ajax.reload();
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


$("#formeditdtform").formValidation({
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
            url: base_url + "/detailform/edit",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#dtformEditModal").modal('hide');
                swal.fire({
                    title: 'Loading',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        swal.showLoading();
                    }
                });
            },
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        dtform.ajax.reload();
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