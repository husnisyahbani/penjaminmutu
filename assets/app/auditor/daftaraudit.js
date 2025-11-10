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
            {"targets": [0,1,2,3], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/daftaraudit/listpertanyaan/"+audit_id,
            "type": "POST"
        }
    });

    $('#jwb_pertanyaan').summernote('code', jwb_pertanyaan);
    $('#jwb_referensi').summernote('code', jwb_referensi);
    $('#jwb_hasil').summernote('code', jwb_hasil);
    $('#jwb_catatan').summernote('code', jwb_catatan);
    $('#jwb_tujuan').summernote('code', jwb_tujuan);


    $("#daftarpertanyaan").on("click", ".delik", function () {
        var audit_id = $(this).attr('audit_id');
        var dtform_id = $(this).attr('dtform_id');
         window.location.href = base_url+'/daftaraudit/delik?audit_id='+audit_id+"&dtform_id="+dtform_id;
    });

    $("#daftaraudit").on("click", ".detail", function () {
       // $('#pertanyaanModal').modal('show');
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

    $("#kembali").on("click", function () {
         var id = $(this).attr('audit_id');
         window.location.href = base_url+'/daftaraudit/detail/'+id;
    });

    $("#submittemuan").on("click", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $jwb_temuan = $('#jwb_temuan').val();

        $.ajax({
            url: base_url + "/daftaraudit/temuan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_temuan:$jwb_temuan
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });

    $("#submitcatatan").on("click", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $jwb_catatan = $('#jwb_catatan').summernote('code');

        $.ajax({
            url: base_url + "/daftaraudit/catatan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_catatan:$jwb_catatan
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });

    $("#submitpertanyaan").on("click", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $jwb_pertanyaan = $('#jwb_pertanyaan').summernote('code');

        $.ajax({
            url: base_url + "/daftaraudit/pertanyaan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_pertanyaan:$jwb_pertanyaan
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
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
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/daftaraudit/tujuan",
            type: "POST",
            data: formData,
            processData: false,        // ✅ wajib
            contentType: false,        // ✅ wajib
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        });
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

    $("#submittujuan").on("click", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $jwb_tujuan = $('#jwb_tujuan').summernote('code');

        $.ajax({
            url: base_url + "/daftaraudit/tujuan",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_tujuan:$jwb_tujuan
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });


    $("#submithasil").on("click", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $jwb_hasil = $('#jwb_hasil').summernote('code');

        $.ajax({
            url: base_url + "/daftaraudit/hasil",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_hasil:$jwb_hasil
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });

    $("#submitreferensi").on("click", function () {
        $dtform_id = $(this).attr('dtform_id');
        $audit_id = $(this).attr('audit_id');
        $jwb_referensi = $('#jwb_referensi').summernote('code');

        $.ajax({
            url: base_url + "/daftaraudit/referensi",
            type: "POST",
            data: {
                dtform_id:$dtform_id,
                audit_id:$audit_id,
                jwb_referensi:$jwb_referensi
            },
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
            success: function (data) {
                swal.close();
                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, org_types) {
                    if (org_types.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        swal.fire("Oops", org_types.pesan, "error");
                    }
                });
            },
            error: function () {
                swal.fire("Oops", "No connection!", "error");
            }
        });
    });

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

});