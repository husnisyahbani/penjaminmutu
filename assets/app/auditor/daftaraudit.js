$(function () {
    var daftaraudit = $('#daftaraudit').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,5,6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listmutu/",
            "type": "POST"
        }
    });

    var daftarpertanyaan = $('#daftarpertanyaan').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,1,2,3,4,5,6], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listpertanyaan",
            "type": "POST"
        }
    });

    var $dtform_id;
    var $audit_id;

    $("#daftarpertanyaan").on("click", ".hasil", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#pertanyaanModal').modal('hide');

        $('#pertanyaanModal').one('hidden.bs.modal', function () {
        $('#hasilModal').modal('show');
        });

        $('#hasilModal').one('hidden.bs.modal', function () {
        setTimeout(() => {
            $('#pertanyaanModal').modal('show');
            $('body').addClass('modal-open');
        }, 300);
        });

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[2];
        var hasil = rowData[4];
        var cleanHasil = hasil.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaanhasil').summernote('code',pertanyaan);
        $('#jwb_hasil').summernote('code',cleanHasil);

    });

    $("#daftarpertanyaan").on("click", ".temuan", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#pertanyaanModal').modal('hide');

        $('#pertanyaanModal').one('hidden.bs.modal', function () {
        $('#temuanModal').modal('show');
        });

        $('#temuanModal').one('hidden.bs.modal', function () {
        setTimeout(() => {
            $('#pertanyaanModal').modal('show');
            $('body').addClass('modal-open');
        }, 300);
        });

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[2];
        var temuan = rowData[5];
        var cleanTemuan = temuan.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaantemuan').summernote('code',pertanyaan);
        $('#jwb_temuan').val(cleanTemuan);

    });

    $("#daftarpertanyaan").on("click", ".catatan", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#pertanyaanModal').modal('hide');

        $('#pertanyaanModal').one('hidden.bs.modal', function () {
        $('#catatanModal').modal('show');
        });

        $('#catatanModal').one('hidden.bs.modal', function () {
        setTimeout(() => {
            $('#pertanyaanModal').modal('show');
            $('body').addClass('modal-open');
        }, 300);
        });

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[2];
        var catatan = rowData[6];
        var cleanCatatan = catatan.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaancatatan').summernote('code',pertanyaan);
        // tampilkan di summernote
        $('#jwb_catatan').summernote('code', cleanCatatan);

    });

    $("#daftaraudit").on("click", ".detail", function () {
        $('#pertanyaanModal').modal('show');
        var id = $(this).attr('id');
        daftarpertanyaan.ajax.url(base_url + "/daftaraudit/listpertanyaan/"+id).load();
    });

    $("#daftaraudit").on("click", ".proses", function () {
        var id = $(this).attr('id');
        proses(id);
    });

    function proses(idku)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Memproses Formulir Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Proses!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/daftaraudit/proses",
                    type: "POST",
                    data: { id: idku}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Formulir Sedang Diproses!",
                                type: "success",
                                preConfirm: function () {
                                    daftaraudit.ajax.reload();
                                }
                            });
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    $("#daftaraudit").on("click", ".selesai", function () {
        var id = $(this).attr('id');
        selesai(id);
    });

    function selesai($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Selesaikan Proses Formulir Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Selesai!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/daftaraudit/selesai",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.fire({
                                title: "Hapus",
                                text: "Formulir Telah Selesai Diproses!",
                                type: "success",
                                preConfirm: function () {
                                    daftaraudit.ajax.reload();
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
        $("#addModal").modal('show');
    });

    $("#formadd").formValidation({
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
            url: base_url + "/daftaraudit/tambah",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
            beforeSend: function () {
                $("#addModal").modal('hide');
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
                        daftaraudit.ajax.reload();
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

$("#formhasil").formValidation({
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
        //var formData = new FormData(e.target);
        var $jwb_hasil = $('#jwb_hasil').summernote('code');
        //var $jwb_hasil = tinymce.get('jwb_hasil').getContent();

        $.ajax({
            url: base_url + "/daftaraudit/hasil",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_hasil:$jwb_hasil
            },
            beforeSend: function () {
                $("#hasilModal").modal('hide');
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
                        daftarpertanyaan.ajax.reload();
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


$("#formtemuan").formValidation({
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
        //var formData = new FormData(e.target);
        var $jwb_temuan = $('#jwb_temuan').val();

        $.ajax({
            url: base_url + "/daftaraudit/temuan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_temuan:$jwb_temuan
            },
            beforeSend: function () {
                $("#temuanModal").modal('hide');
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
                        daftarpertanyaan.ajax.reload();
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


$("#formcatatan").formValidation({
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
        //var formData = new FormData(e.target);
        var $jwb_catatan = $('#jwb_catatan').summernote('code');
        //var $jwb_catatan = tinymce.get('jwb_catatan').getContent();

        $.ajax({
            url: base_url + "/daftaraudit/catatan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_catatan:$jwb_catatan
            },
            beforeSend: function () {
                $("#catatanModal").modal('hide');
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
                        daftarpertanyaan.ajax.reload();
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

});