$(function () {
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

$("#editreferensi").on("click", function () {
    // Ambil isi dari elemen <p id="tujuan">
    var isiReferensi = $("#referensi").html();

    // Tampilkan modal
    $('#referensiModal').modal('show');

    // Setelah modal tampil, masukkan isi ke Summernote
    $('#referensiModal').on('shown.bs.modal', function () {
        $('#jwb_referensi').summernote('code', isiReferensi);
    });
});


$("#editpertanyaan").on("click", function () {
    // Ambil isi dari elemen <p id="tujuan">
    var isiPertanyaan = $("#pertanyaan").html();

    // Tampilkan modal
    $('#pertanyaanModal').modal('show');

    // Setelah modal tampil, masukkan isi ke Summernote
    $('#pertanyaanModal').on('shown.bs.modal', function () {
        $('#jwb_pertanyaan').summernote('code', isiPertanyaan);
    });
});

$("#edithasil").on("click", function () {
    // Ambil isi dari elemen <p id="tujuan">
    var isiHasil = $("#hasil").html();

    // Tampilkan modal
    $('#hasilModal').modal('show');

    // Setelah modal tampil, masukkan isi ke Summernote
    $('#hasilModal').on('shown.bs.modal', function () {
        $('#jwb_hasil').summernote('code', isiHasil);
    });
});


$("#editcatatan").on("click", function () {
    // Ambil isi dari elemen <p id="tujuan">
    var isiCatatan = $("#catatan").html();

    // Tampilkan modal
    $('#catatanModal').modal('show');

    // Setelah modal tampil, masukkan isi ke Summernote
    $('#catatanModal').on('shown.bs.modal', function () {
        $('#jwb_catatan').summernote('code', isiCatatan);
    });
});



    $("#edittemuan").on("click", function () {
       $('#temuanModal').modal('show');
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