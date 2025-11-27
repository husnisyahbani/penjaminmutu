$(function () {

    var tiliklist = $('#tilik').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            {"targets": [0,5], "orderable": false}
        ],
        "ajax": {
            "url": base_url + "/delik/listdelik/"+jwb_id,
            "type": "POST"
        }
    });

    $("#tambahtilik").on("click", function () {
        $('#tilikModal').modal('show');
    });

    $("#tilik").on("click", ".edithasil", function () {
        var id = $(this).attr('id');
        gethasilById(id);
    });

    function gethasilById($id)
    {
        $.ajax({
            url: base_url + "/delik/getjawabById/"+$id,
            type: "GET",
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
                        if(org_types.status){
                            $("#edit_dtjwb_hasil").val(org_types.dtjwb_hasil);
                            $("#hasil_dtjwb_id").val(org_types.dtjwb_id);
                            $('#editHasilModal').modal('show');
                        }else{
                            swal.fire("Oops", "Gagal", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        }); 
    }


    $("#tilik").on("click", ".editpertanyaan", function () {
        var id = $(this).attr('id');
        getjawabById(id);
    });

    function getjawabById($id)
    {
        $.ajax({
            url: base_url + "/delik/getjawabById/"+$id,
            type: "GET",
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
                        if(org_types.status){
                            $("#edit_dtjwb_pertanyaan").val(org_types.dtjwb_pertanyaan);
                            $("#pertanyaan_dtjwb_id").val(org_types.dtjwb_id);
                            $('#editPertanyaanModal').modal('show');
                        }else{
                            swal.fire("Oops", "Gagal", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        }); 
    }

    $("#tilik").on("click", ".delete", function () {
        var id = $(this).attr('id');
        hapus(id);
    });

    function hapus($id)
    {
        swal.fire({
            title: "Anda Yakin?",
            text: "Anda Yakin Ingin Menghapus Data Tilik Ini?",
            type: "warning",
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: 'Tidak',
            preConfirm: function () {
                $.ajax({
                    url: base_url + "/delik/hapus",
                    type: "POST",
                    data: { id: $id}
                })
                        .done(function (data) {
                            swal.close();
                            tiliklist.ajax.reload();
                            if(data.status == true){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data telah terhapus.',
                                    showConfirmButton: false,
                                    timer: 1200
                                });
                            }
                        })
                        .error(function (data) {
                            swal.fire("Oops", "No connection!", "error");
                        });
            }
        });
    }

    $("#tilik").on("click", ".edittemuan", function () {
        var id = $(this).attr('id');
        gettemuanById(id);
    });

    function gettemuanById($id)
    {
        $.ajax({
            url: base_url + "/delik/getjawabById/"+$id,
            type: "GET",
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
                        if(org_types.status){
                            $("#edit_dtjwb_temuan").val(org_types.dtjwb_temuan);
                            $("#temuan_dtjwb_id").val(org_types.dtjwb_id);
                            $('#editTemuanModal').modal('show');
                        }else{
                            swal.fire("Oops", "Gagal", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        }); 
    }


    $("#tilik").on("click", ".editcatatan", function () {
        var id = $(this).attr('id');
        getcatatanById(id);
    });

    function getcatatanById($id)
    {
        $.ajax({
            url: base_url + "/delik/getjawabById/"+$id,
            type: "GET",
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
                        if(org_types.status){
                            $("#edit_dtjwb_catatan").val(org_types.dtjwb_catatan);
                            $("#catatan_dtjwb_id").val(org_types.dtjwb_id);
                            $('#editCatatanModal').modal('show');
                        }else{
                            swal.fire("Oops", "Gagal", "error");
                        }
                    });
                    
            },
            error: function (json) {
                swal.fire("Oops", "No connection!", "error");
                
            }
        }); 
    }

    $("#formpertanyaan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/delik/pertanyaan",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#editPertanyaanModal").modal('hide');
                Swal.fire({
                    title: 'Loading...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (data) {
                Swal.close();

                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, res) {
                    if (res.status) {
                        tiliklist.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        Swal.fire("Oops", res.pesan, "error");
                    }
                });

                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                Swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

        return false;
    });

    $("#formhasil").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/delik/hasil",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#editHasilModal").modal('hide');
                Swal.fire({
                    title: 'Loading...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (data) {
                Swal.close();

                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, res) {
                    if (res.status) {
                        tiliklist.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        Swal.fire("Oops", res.pesan, "error");
                    }
                });

                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                Swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

        return false;
    });

    $("#formtemuan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/delik/temuan",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#editTemuanModal").modal('hide');
                Swal.fire({
                    title: 'Loading...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (data) {
                Swal.close();

                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, res) {
                    if (res.status) {
                        tiliklist.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        Swal.fire("Oops", res.pesan, "error");
                    }
                });

                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                Swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

        return false;
    });

    $("#formcatatan").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/delik/catatan",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#editCatatanModal").modal('hide');
                Swal.fire({
                    title: 'Loading...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (data) {
                Swal.close();

                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, res) {
                    if (res.status) {
                        tiliklist.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        Swal.fire("Oops", res.pesan, "error");
                    }
                });

                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                Swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

        return false;
    });

    $("#formtilik").formValidation({
        framework: "bootstrap4",
        excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
    }).on('success.form.fv', function (e) {
        e.preventDefault();

        var $form = $(e.target);
        var formData = new FormData(e.target);

        $.ajax({
            url: base_url + "/delik/tambahtilik",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $("#tilikModal").modal('hide');
                Swal.fire({
                    title: 'Loading...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    onOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (data) {
                Swal.close();

                var list = data == null ? [] : (data instanceof Array ? data : [data]);
                $.each(list, function (index, res) {
                    if (res.status) {
                        tiliklist.ajax.reload();
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Data telah tersimpan.',
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        Swal.fire("Oops", res.pesan, "error");
                    }
                });

                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            },
            error: function () {
                Swal.fire("Oops", "No connection!", "error");
                $form.formValidation('disableSubmitButtons', false)
                    .formValidation('resetForm', true);
            }
        });

        return false;
    });

    $("#edittujuan").on("click", function () {
        // Ambil isi dari elemen <p id="tujuan">
        var isiTujuan = $("#tujuan").html();

        // Tampilkan modal
        $('#tujuanModal').modal('show');

        // Setelah modal tampil, masukkan isi ke Summernote
        $('#tujuanModal').on('shown.bs.modal', function () {
            $('#jwb_tujuan').summernote('code', isiTujuan);
        });
    });

    $("#kembali").on("click", function () {
        var id = $(this).attr('audit_id');
        window.location.href = base_url+'/daftaraudit/detail/'+id;
    });



   $("#formtujuan").formValidation({
    framework: "bootstrap4",
    excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
}).on('success.form.fv', function (e) {
    e.preventDefault();

    var $form = $(e.target);
    var formData = new FormData(e.target);

    var isiTujuan = $('#jwb_tujuan').summernote('code');
    formData.set('jwb_tujuan', isiTujuan);

    $.ajax({
        url: base_url + "/delik/tujuan",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#tujuanModal").modal('hide');
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();

            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, res) {
                if (res.status) {
                    $("#tujuan").html(isiTujuan);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire("Oops", res.pesan, "error");
                }
            });

            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        }
    });

    return false;
});


$("#formreferensi").formValidation({
    framework: "bootstrap4",
    excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
}).on('success.form.fv', function (e) {
    e.preventDefault();

    var $form = $(e.target);
    var formData = new FormData(e.target);

    var isiReferensi = $('#jwb_referensi').summernote('code');
    formData.set('jwb_referensi', isiReferensi);

    $.ajax({
        url: base_url + "/delik/referensi",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#referensiModal").modal('hide');
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();

            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, res) {
                if (res.status) {
                    $("#referensi").html(isiReferensi);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire("Oops", res.pesan, "error");
                }
            });

            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        }
    });

    return false;
});


$("#formpertanyaan").formValidation({
    framework: "bootstrap4",
    excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
}).on('success.form.fv', function (e) {
    e.preventDefault();

    var $form = $(e.target);
    var formData = new FormData(e.target);

    var isiPertanyaan = $('#jwb_pertanyaan').summernote('code');
    formData.set('jwb_pertanyaan', isiPertanyaan);

    $.ajax({
        url: base_url + "/delik/pertanyaan",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#pertanyaanModal").modal('hide');
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();

            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, res) {
                if (res.status) {
                    $("#pertanyaan").html(isiPertanyaan);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire("Oops", res.pesan, "error");
                }
            });

            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        }
    });

    return false;
});


$("#formhasil").formValidation({
    framework: "bootstrap4",
    excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
}).on('success.form.fv', function (e) {
    e.preventDefault();

    var $form = $(e.target);
    var formData = new FormData(e.target);

    var isiHasil = $('#jwb_hasil').summernote('code');
    formData.set('jwb_hasil', isiHasil);

    $.ajax({
        url: base_url + "/delik/hasil",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#hasilModal").modal('hide');
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();

            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, res) {
                if (res.status) {
                    $("#hasil").html(isiHasil);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire("Oops", res.pesan, "error");
                }
            });

            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        }
    });

    return false;
});


$("#formtemuan").formValidation({
    framework: "bootstrap4",
    excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
}).on('success.form.fv', function (e) {
    e.preventDefault();

    var $form = $(e.target);
    var formData = new FormData(e.target);
    var isiTemuan = $('#jwb_temuan').val();

    $.ajax({
        url: base_url + "/delik/temuan",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#temuanModal").modal('hide');
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();

            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, res) {
                if (res.status) {
                    $("#temuan").text(isiTemuan);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire("Oops", res.pesan, "error");
                }
            });

            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        }
    });

    return false;
});


$("#formcatatan").formValidation({
    framework: "bootstrap4",
    excluded: [':disabled', ':hidden', ':not(:visible)'], // ðŸ”§ tambahkan agar summernote tidak dianggap kosong
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
}).on('success.form.fv', function (e) {
    e.preventDefault();

    var $form = $(e.target);
    var formData = new FormData(e.target);

    var isiCatatan = $('#jwb_catatan').summernote('code');
    formData.set('jwb_catatan', isiCatatan);

    $.ajax({
        url: base_url + "/delik/catatan",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $("#catatanModal").modal('hide');
            Swal.fire({
                title: 'Loading...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function (data) {
            Swal.close();

            var list = data == null ? [] : (data instanceof Array ? data : [data]);
            $.each(list, function (index, res) {
                if (res.status) {
                    $("#catatan").html(isiCatatan);

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data telah tersimpan.',
                        showConfirmButton: false,
                        timer: 1200
                    });
                } else {
                    Swal.fire("Oops", res.pesan, "error");
                }
            });

            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        },
        error: function () {
            Swal.fire("Oops", "No connection!", "error");
            $form.formValidation('disableSubmitButtons', false)
                .formValidation('resetForm', true);
        }
    });

    return false;
});


});