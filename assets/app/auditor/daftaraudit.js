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
            {"targets": [0,1,2,3,4,5,6,7,8], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listpertanyaan/"+audit_id,
            "type": "POST"
        }
    });

    var $dtform_id;
    var $audit_id;

    $("#daftarpertanyaan").on("click", ".tujuan", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#tujuanModal').modal('show');
        $('#pertanyaantujuan').summernote('code',"");
        // // tampilkan di summernote
         $('#jwb_tujuan').summernote('code', "");
        

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[1];
        var tujuan = rowData[3];
        var cleanTujuan = tujuan.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaanreferensi').summernote('code',pertanyaan);
        $('#jwb_tujuan').summernote('code',cleanTujuan);

    });

    $("#daftarpertanyaan").on("click", ".referensi", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#referensiModal').modal('show');
        $('#pertanyaanreferensi').summernote('code',"");
        // // tampilkan di summernote
         $('#jwb_referensi').summernote('code', "");
        

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[1];
        var referensi = rowData[4];
        var cleanReferensi = referensi.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaanreferensi').summernote('code',pertanyaan);
        $('#jwb_referensi').summernote('code',cleanReferensi);

    });

    $("#daftarpertanyaan").on("click", ".pertanyaan", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#pertanyaanModal').modal('show');
        $('#pertanyaanpertanyaan').summernote('code',"");
        // // tampilkan di summernote
         $('#jwb_pertanyaan').summernote('code', "");
        

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[1];
        var lingkuppertanyaan = rowData[5];
        var cleanPertanyaan = lingkuppertanyaan.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaanpertanyaan').summernote('code',pertanyaan);
        $('#jwb_pertanyaan').summernote('code',cleanPertanyaan);

    });

    $("#daftarpertanyaan").on("click", ".hasil", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#hasilModal').modal('show');
        $('#pertanyaanhasil').summernote('code',"");
        // // tampilkan di summernote
         $('#jwb_hasil').summernote('code', "");
        

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[1];
        var hasil = rowData[6];
        var cleanHasil = hasil.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaanhasil').summernote('code',pertanyaan);
        $('#jwb_hasil').summernote('code',cleanHasil);

    });

    $("#daftarpertanyaan").on("click", ".temuan", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#temuanModal').modal('show');
        $('#pertanyaantemuan').summernote('code',"");
        // // tampilkan di summernote
         $('#jwb_temuan').summernote('code', "");

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[1];
        var temuan = rowData[7];
        var cleanTemuan = temuan.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaantemuan').summernote('code',pertanyaan);
        $('#jwb_temuan').val(cleanTemuan);

    });

    $("#daftarpertanyaan").on("click", ".catatan", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $('#catatanModal').modal('show');

        $('#pertanyaancatatan').summernote('code',"");
        // // tampilkan di summernote
         $('#jwb_catatan').summernote('code', "");

        var tr = $(this).closest('tr');
        var rowData = daftarpertanyaan.row(tr).data();
        var pertanyaan = rowData[1];
        var catatan = rowData[8];
        var cleanCatatan = catatan.replace(/<button[\s\S]*$/i, '');
        $('#pertanyaancatatan').summernote('code',pertanyaan);
        // tampilkan di summernote
        $('#jwb_catatan').summernote('code', cleanCatatan);

    });

    $("#daftaraudit").on("click", ".detail", function () {
        $('#pertanyaanModal').modal('show');
        var id = $(this).attr('id');
         window.location.href = base_url+'/daftaraudit/detail/'+id;
        //daftarpertanyaan.ajax.url(base_url + "/daftaraudit/listpertanyaan/"+id).load();
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
                                title: "Diproses",
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
                                title: "Selesai",
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


$("#formtujuan").formValidation({
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
        var $jwb_tujuan = $('#jwb_tujuan').summernote('code');
        //var $jwb_catatan = tinymce.get('jwb_catatan').getContent();

        $.ajax({
            url: base_url + "/daftaraudit/tujuan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_tujuan:$jwb_tujuan
            },
            beforeSend: function () {
                $("#tujuanModal").modal('hide');
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

$("#formreferensi").formValidation({
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
        var $jwb_referensi = $('#jwb_referensi').summernote('code');
        //var $jwb_catatan = tinymce.get('jwb_catatan').getContent();

        $.ajax({
            url: base_url + "/daftaraudit/referensi",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_referensi:$jwb_referensi
            },
            beforeSend: function () {
                $("#referensiModal").modal('hide');
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

$("#formpertanyaan").formValidation({
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
        var $jwb_pertanyaan = $('#jwb_pertanyaan').summernote('code');
        //var $jwb_catatan = tinymce.get('jwb_catatan').getContent();

        $.ajax({
            url: base_url + "/daftaraudit/pertanyaan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_pertanyaan:$jwb_pertanyaan
            },
            beforeSend: function () {
                $("#pertanyaanModal").modal('hide');
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