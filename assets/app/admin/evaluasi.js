$(function () {

    var datalist = $('#data').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,3,6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/evaluasi/listdata",
            "type": "POST"
        }
    });

    $("#data").on("click", ".edit", function () {
        var id = $(this).attr('id');
        getdata(id);
    });

    $("#data").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapusdata(id);
    });
    
    $("#data").on("click", ".show", function () {
        var id = $(this).attr('id');
        showdata(id);
    });

    $("#data").on("click", ".hide", function () {
        var id = $(this).attr('id');
        hidedata(id);
    });

    $("#tambahdata").on("click", function () {
         $('#dataAddModal').modal('show');
    });

    function getdata(id)
    {
        $.ajax({
            url: base_url + "/evaluasi/getData/"+id,
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
                    $('#dataEditModal').modal('show');
                    $("#edit_data_uraian").val(list.data_uraian);
                    $("#edit_data_keterangan").val(list.data_keterangan);
                    $("#edit_data_kategori").val(list.data_kategori);
                    $("#data_id").val(list.data_id);
                } else {
                    swal.fire("Oops", "Gagal!", "error");
                        $("#formeditdata")
                    .formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
                }   
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
                
                 $("#formeditdata")
                .formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
            }
        });
    }

    function hapusdata($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Data Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/evaluasi/hapus",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Data Telah Terhapus!",
                                type: "success",
                                preConfirm: function () {
                                    datalist.ajax.reload();
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    function showdata($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Menampilkan Data Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Tampilkan!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/data/show",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Tampilkan",
                                text: "Data Telah Ditampilkan!",
                                type: "success",
                                preConfirm: function () {
                                    datalist.ajax.reload();
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }


    function hidedata($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Menyembunyikan Data Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Sembunyikan!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/data/hide",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Sembunyikan",
                                text: "Data Telah Disembunyikan!",
                                type: "success",
                                preConfirm: function () {
                                    datalist.ajax.reload();
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    $("#formadddata").formValidation({
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
            url: base_url + "/evaluasi/tambah",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#dataAddModal").modal('hide');
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
                        datalist.ajax.reload();
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
});


$("#formeditdata").formValidation({
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
            url: base_url + "/evaluasi/edit",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#dataEditModal").modal('hide');
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
                        datalist.ajax.reload();
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
});

  
})